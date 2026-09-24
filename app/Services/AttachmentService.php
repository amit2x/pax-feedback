<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\FeedbackAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class AttachmentService
{
    private const ALLOWED_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    private const ALLOWED_AUDIO_MIMES = ['audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/wav', 'audio/mp4', 'video/webm'];
    private const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8', 'pht', 'phps',
        'exe', 'sh', 'bat', 'cmd', 'com', 'scr', 'msi', 'jar', 'vbs', 'ps1',
        'js', 'mjs', 'html', 'htm', 'xhtml', 'svg', 'xml',
    ];
    private const MAX_IMAGE_BYTES = 5_242_880;     // 5 MB
    private const MAX_AUDIO_BYTES = 10_485_760;    // 10 MB
    private const MAX_AUDIO_SECONDS = 60;

    public function store(Feedback $feedback, UploadedFile $file, string $type): ?FeedbackAttachment
    {
        Log::info('AttachmentService::store called', [
            'feedback_id'  => $feedback->id,
            'type'         => $type,
            'original_name'=> $file->getClientOriginalName(),
            'client_mime'  => $file->getClientMimeType(),
            'real_mime'    => $file->getMimeType(),
            'size'         => $file->getSize(),
            'is_valid'     => $file->isValid(),
            'error_code'   => $file->getError(),
            'real_path'    => $file->getRealPath(),
        ]);
        try {
            $this->assertAllowed($file, $type);

            $ulid = (string) Str::ulid();
            $disk = config('feedback.media_disk', 'local');

            if ($type === 'photo') {
                [$storedName, $mime, $ext, $size] = $this->processImage($file, $ulid, $disk);
            } else {
                [$storedName, $mime, $ext, $size] = $this->processAudio($file, $ulid, $disk);
            }

            $path = "feedback/{$type}s/{$storedName}";

            return FeedbackAttachment::create([
                'uuid' => (string) Str::uuid(),
                'feedback_id' => $feedback->id,
                'type' => $type,
                'original_name' => null,
                'stored_name' => $storedName,
                'mime_type' => $mime,
                'extension' => $ext,
                'size' => $size,
                'storage_disk' => $disk,
                'storage_path' => $path,
                'checksum' => hash_file('sha256', $file->getRealPath()),
                'scan_status' => 'pending',
                'processing_status' => 'completed',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Attachment store failed', [
                'feedback_id' => $feedback->id,
                'type' => $type,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'error' => $e->getMessage(),
            ]);

            // Re-throw so the caller's transaction rolls back atomically.
            throw $e;
        }
    }

    private function assertAllowed(UploadedFile $file, string $type): void
    {
        if (! $file->isValid()) {
            throw new \InvalidArgumentException('File upload failed at transport layer.');
        }

        $allowed = $type === 'photo' ? self::ALLOWED_IMAGE_MIMES : self::ALLOWED_AUDIO_MIMES;
        $maxBytes = $type === 'photo' ? self::MAX_IMAGE_BYTES : self::MAX_AUDIO_BYTES;

        if ($file->getSize() > $maxBytes) {
            throw new \InvalidArgumentException('File exceeds maximum allowed size.');
        }

        // Real MIME via finfo (not the client-supplied value)
        $realMime = $file->getMimeType();
        if (! in_array($realMime, $allowed, true)) {
            throw new \InvalidArgumentException("Unsupported MIME type: {$realMime}");
        }

        // Reject dangerous extensions even if the MIME looks fine
        $ext = strtolower((string) $file->getClientOriginalExtension());
        if (in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Disallowed file extension.');
        }

        // Reject double extensions
        if (substr_count($ext, '.') > 0) {
            throw new \InvalidArgumentException('Double extension not allowed.');
        }

        // For images: verify PHP can decode it via getimagesize
        if ($type === 'photo') {
            $info = @getimagesize($file->getRealPath());
            if ($info === false) {
                throw new \InvalidArgumentException('File is not a valid image.');
            }
            if (! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
                throw new \InvalidArgumentException('Image type not permitted.');
            }
        }

        // Reject polyglot signatures (GIF header in a JPEG, etc.)
        $this->assertNotPolyglot($file);
    }

    private function assertNotPolyglot(UploadedFile $file): void
    {
        $fp = fopen($file->getRealPath(), 'rb');
        if (! $fp) return;

        $head = fread($fp, 16);
        fclose($fp);

        if ($head === false) return;

        // Reject if it looks like PHP, HTML, or a script
        $forbiddenSignatures = [
            '<?php', '<?=', '<script', '<html', '<!DOCTYPE', '#!/',
        ];

        foreach ($forbiddenSignatures as $sig) {
            if (stripos($head, $sig) !== false) {
                throw new \InvalidArgumentException('File contains disallowed content.');
            }
        }
    }

    /**
     * @return array{0:string,1:string,2:string,3:int}
     */
    private function processImage(UploadedFile $file, string $ulid, string $disk): array
    {
        $manager = $this->imageManager();
        $image = $manager->read($file->getRealPath());

        // Scale down if larger than 2400px on either side
        $image->scaleDown(width: 2400, height: 2400);

        // Re-encode to WebP — strips EXIF/GPS and any embedded payloads
        $encoded = $image->toWebp(quality: 82)->toString();

        $storedName = "{$ulid}.webp";
        Storage::disk($disk)->put("feedback/photos/{$storedName}", $encoded);

        return [$storedName, 'image/webp', 'webp', strlen($encoded)];
    }

    /**
     * @return array{0:string,1:string,2:string,3:int}
     */
    private function processAudio(UploadedFile $file, string $ulid, string $disk): array
    {
        $bytes = file_get_contents($file->getRealPath());
        if ($bytes === false || strlen($bytes) === 0) {
            throw new \RuntimeException('Could not read audio file.');
        }

        // Choose a safe extension based on the real MIME, not the client's filename
        $mime = $file->getMimeType();
        $ext = match (true) {
            str_contains($mime, 'ogg') => 'ogg',
            str_contains($mime, 'mp4') => 'm4a',
            str_contains($mime, 'mpeg') => 'mp3',
            str_contains($mime, 'wav') => 'wav',
            default => 'webm',
        };

        $storedName = "{$ulid}.{$ext}";
        Storage::disk($disk)->put("feedback/voices/{$storedName}", $bytes);

        return [$storedName, $mime, $ext, strlen($bytes)];
    }

    private function imageManager(): ImageManager
    {
        if (extension_loaded('imagick')) {
            return new ImageManager(new ImagickDriver());
        }
        if (extension_loaded('gd')) {
            return new ImageManager(new GdDriver());
        }
        throw new \RuntimeException('No image processing extension available (gd or imagick required).');
    }
}
