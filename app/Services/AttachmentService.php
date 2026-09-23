<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\FeedbackAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class AttachmentService
{
    private const ALLOWED_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    private const ALLOWED_AUDIO_MIMES = ['audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/wav'];

    private const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'pht',
        'exe', 'sh', 'bat', 'cmd', 'com', 'scr', 'msi', 'jar',
        'js', 'mjs', 'html', 'htm', 'svg', 'xml', 'xhtml',
    ];

    public function store(Feedback $feedback, UploadedFile $file, string $type): FeedbackAttachment
    {
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
    }

    private function assertAllowed(UploadedFile $file, string $type): void
    {
        $allowed = $type === 'photo' ? self::ALLOWED_IMAGE_MIMES : self::ALLOWED_AUDIO_MIMES;

        $realMime = $file->getMimeType();
        if (! in_array($realMime, $allowed, true)) {
            throw new \InvalidArgumentException('Unsupported MIME type.');
        }

        $ext = strtolower((string) $file->getClientOriginalExtension());
        if (in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Disallowed extension.');
        }

        $safeExts = $type === 'photo'
            ? ['jpg', 'jpeg', 'png', 'webp']
            : ['webm', 'ogg', 'mp3', 'wav', 'oga'];

        if ($ext !== '' && ! in_array($ext, $safeExts, true)) {
            throw new \InvalidArgumentException('Unexpected extension.');
        }

        if (! $file->isValid()) {
            throw new \InvalidArgumentException('Upload is not valid.');
        }
    }

    private function processImage(UploadedFile $file, string $ulid, string $disk): array
    {
        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($file->getRealPath());

        // Re-encode to WebP; strips EXIF, GPS, and any embedded payloads.
        $image->resize(2400, 2400, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $encoded = (string) $image->encode('webp', 82);
        $storedName = "{$ulid}.webp";
        Storage::disk($disk)->put("feedback/photos/{$storedName}", $encoded);

        return [$storedName, 'image/webp', 'webp', strlen($encoded)];
    }

    private function processAudio(UploadedFile $file, string $ulid, string $disk): array
    {
        $bytes = file_get_contents($file->getRealPath());
        if ($bytes === false) {
            throw new \RuntimeException('Could not read audio file.');
        }

        $storedName = "{$ulid}.webm";
        Storage::disk($disk)->put("feedback/voices/{$storedName}", $bytes);

        return [$storedName, 'audio/webm', 'webm', strlen($bytes)];
    }
}
