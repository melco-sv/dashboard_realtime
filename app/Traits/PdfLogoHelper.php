<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait PdfLogoHelper
{
    /**
     * Returns base64-encoded logo, resized to display size using GD (PNG only).
     * Result is cached for 24 hours to avoid re-processing on every request.
     */
    private function logoBase64(string $filename, int $maxW = 220, int $maxH = 120): string
    {
        $path = public_path("assets/{$filename}");
        if (!file_exists($path)) return '';

        // Cache key includes file modification time so cache invalidates if file changes
        $cacheKey = 'pdf_logo_' . md5($filename . filemtime($path));

        return Cache::remember($cacheKey, 86400, function () use ($path, $maxW, $maxH) {
            if (function_exists('imagecreatefrompng')) {
                try {
                    $src = @imagecreatefrompng($path);
                    if ($src) {
                        $srcW  = imagesx($src);
                        $srcH  = imagesy($src);
                        $ratio = min($maxW / $srcW, $maxH / $srcH, 1.0);
                        $newW  = (int) round($srcW * $ratio);
                        $newH  = (int) round($srcH * $ratio);

                        $dst = imagecreatetruecolor($newW, $newH);
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                        imagefilledrectangle($dst, 0, 0, $newW - 1, $newH - 1, $transparent);
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

                        ob_start();
                        imagepng($dst, null, 6);
                        $data = ob_get_clean();

                        imagedestroy($src);
                        imagedestroy($dst);

                        if ($data) {
                            return 'data:image/png;base64,' . base64_encode($data);
                        }
                    }
                } catch (\Throwable $e) {
                    // Fall through to raw read
                }
            }

            return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        });
    }
}
