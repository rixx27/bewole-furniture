<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Compress and optimize an uploaded image to WebP format, resizing if necessary.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $directory  Target storage directory (e.g. 'products/thumbnails')
     * @param  int  $maxWidth  Maximum allowable width in pixels (default: 1200)
     * @param  int  $maxHeight  Maximum allowable height in pixels (default: 1200)
     * @param  int  $quality  Compression quality 1-100 (default: 82)
     * @param  string  $disk  Storage disk name (default: 'public')
     * @return string Stored file path relative to disk
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1200,
        int $maxHeight = 1200,
        int $quality = 82,
        string $disk = 'public'
    ): string {
        // Fallback if GD or imagewebp is not available
        if (! extension_loaded('gd') || ! function_exists('imagewebp') || ! function_exists('imagecreatefromstring')) {
            return $file->store($directory, $disk);
        }

        try {
            $realPath = $file->getRealPath();
            if (! $realPath || ! file_exists($realPath)) {
                return $file->store($directory, $disk);
            }

            $contents = file_get_contents($realPath);
            if ($contents === false) {
                return $file->store($directory, $disk);
            }

            $sourceImage = @imagecreatefromstring($contents);
            if (! $sourceImage) {
                return $file->store($directory, $disk);
            }

            $origWidth = imagesx($sourceImage);
            $origHeight = imagesy($sourceImage);

            if ($origWidth <= 0 || $origHeight <= 0) {
                return $file->store($directory, $disk);
            }

            // Calculate scaling ratio
            $widthRatio = $maxWidth / $origWidth;
            $heightRatio = $maxHeight / $origHeight;
            $ratio = min($widthRatio, $heightRatio, 1.0);

            $targetWidth = (int) max(1, round($origWidth * $ratio));
            $targetHeight = (int) max(1, round($origHeight * $ratio));

            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

            // Handle transparency preservation (PNG / WebP / GIF)
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

            imagecopyresampled(
                $canvas,
                $sourceImage,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $origWidth,
                $origHeight
            );

            // Export to WebP binary stream
            ob_start();
            $success = imagewebp($canvas, null, $quality);
            $webpData = ob_get_clean();

            if (! $success || empty($webpData)) {
                return $file->store($directory, $disk);
            }

            // Generate unique filename
            $filename = Str::random(40).'.webp';
            $targetPath = trim($directory, '/').'/'.$filename;

            Storage::disk($disk)->put($targetPath, $webpData);

            return $targetPath;
        } catch (\Throwable $e) {
            Log::warning('ImageOptimizerService failed to compress image, falling back to standard store: '.$e->getMessage());

            return $file->store($directory, $disk);
        }
    }
}
