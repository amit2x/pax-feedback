<?php

namespace App\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Writer;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use RuntimeException;
use Throwable;

class QrImageService
{
    private ?ImageManager $imageManager = null;

    /**
     * Generate QR PNG.
     */
    public function png(
        string $payload,
        int $size = 800,
        ?string $logoPath = null
    ): string {
        $size = max(400, min($size, 2400));

        /*
         * Generate QR with HIGH error correction.
         *
         * This is important because the center of the QR
         * will be partially covered by the logo.
         */
        $qrBinary = $this->renderQrPng($payload, $size);

        if (! $logoPath || ! is_readable($logoPath)) {
            return $qrBinary;
        }

        try {
            return $this->compositeLogo(
                $qrBinary,
                $logoPath,
                $size
            );
        } catch (Throwable $e) {
            \Log::warning(
                'QR logo compositing failed; returning plain QR.',
                [
                    'error' => $e->getMessage(),
                    'logo' => $logoPath,
                    'size' => $size,
                ]
            );

            return $qrBinary;
        }
    }

    /**
     * Generate Data URI.
     */
    public function dataUri(
        string $payload,
        int $size = 400,
        ?string $logoPath = null
    ): string {
        return 'data:image/png;base64,'.
            base64_encode(
                $this->png(
                    $payload,
                    $size,
                    $logoPath
                )
            );
    }

    /**
     * Render QR code.
     */
    private function renderQrPng(
        string $payload,
        int $size
    ): string {
        if (! extension_loaded('gd')) {
            throw new RuntimeException(
                'GD extension is required for QR code generation.'
            );
        }

        /*
         * Keep a proper QR quiet zone.
         */
        $margin = max(
            12,
            (int) round($size * 0.025)
        );

        $renderer = new GDLibRenderer(
            $size,
            $margin,
            'png',
            9,
            Fill::uniformColor(
                new Rgb(255, 255, 255),
                new Rgb(15, 23, 42)
            )
        );

        /*
         * IMPORTANT:
         *
         * The default Writer error correction is LOW (L).
         *
         * Because we are placing a logo over the QR,
         * explicitly use HIGH error correction.
         */
        $writer = new Writer($renderer);

        return $writer->writeString(
            $payload,
            'UTF-8',
            ErrorCorrectionLevel::H()
        );
    }

    /**
     * Composite logo into the QR.
     */
    private function compositeLogo(
        string $qrBinary,
        string $logoPath,
        int $size
    ): string {
        $manager = $this->imageManager();

        /*
         * Read QR.
         */
        $qr = $manager->read($qrBinary);

        /*
         * Read logo.
         */
        $logo = $manager->read($logoPath);

        /*
         * ---------------------------------------------------------
         * REMOVE EXCESS WHITE SPACE
         * ---------------------------------------------------------
         *
         * Your source AAI logo contains a large white canvas.
         * Trim it before calculating the logo size.
         */
        $logo->trim(15);

        if ($logo->width() <= 0 || $logo->height() <= 0) {
            throw new RuntimeException(
                'Invalid logo dimensions after trimming.'
            );
        }

        /*
         * ---------------------------------------------------------
         * LOGO SIZE
         * ---------------------------------------------------------
         *
         * Keep actual logo around 10% of QR.
         *
         * This is intentionally smaller than the previous 13%.
         *
         * 800px  -> 80px
         * 1600px -> 160px
         * 2400px -> 240px
         */
        $logoSize = (int) round($size * 0.10);

        $logoSize = max(64, $logoSize);
        $logoSize = min(240, $logoSize);

        /*
         * Preserve aspect ratio.
         */
        $logo->scale(
            width: $logoSize,
            height: $logoSize
        );

        /*
         * ---------------------------------------------------------
         * WHITE PLATE
         * ---------------------------------------------------------
         *
         * Only slightly larger than logo.
         *
         * Logo = approximately 10%
         * Plate = approximately 12%
         *
         * Do NOT use a huge white plate.
         */
        $plateSize = (int) round($size * 0.12);

        $plateSize = max(
            $logoSize + 10,
            $plateSize
        );

        $plateSize = min(
            280,
            $plateSize
        );

        /*
         * Create white center plate.
         */
        $plate = $manager
            ->create(
                $plateSize,
                $plateSize
            )
            ->fill('#ffffff');

        /*
         * ---------------------------------------------------------
         * CENTER PLATE
         * ---------------------------------------------------------
         */
        $qr->place(
            $plate,
            'center'
        );

        /*
         * ---------------------------------------------------------
         * CENTER LOGO
         * ---------------------------------------------------------
         */
        $qr->place(
            $logo,
            'center'
        );

        /*
         * Return final PNG.
         */
        return (string) $qr->toPng();
    }

    /**
     * Image manager.
     */
    private function imageManager(): ImageManager
    {
        if ($this->imageManager === null) {
            $this->imageManager = new ImageManager(
                new GdDriver
            );
        }

        return $this->imageManager;
    }
}
