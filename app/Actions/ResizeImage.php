<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ResizeImage
{
    public static function scaleDown(string $disk, string $path, int $width = 800): void
    {
        $filePath = Storage::disk($disk)->path($path);

        if (!file_exists($filePath)) {
            Log::error('Tried to resize image but file not found: ' . $filePath);
            return;
        }

        $mimeType = mime_content_type($filePath);

        if ($mimeType == 'image/jpeg' || $mimeType == 'image/png' || $mimeType == 'image/gif' || $mimeType == 'image/webp') {
            try {
                $manager = new ImageManager(Driver::class);
                $manager->read($filePath)->scaleDown(width: $width)->save($filePath);
            } catch (\Exception $e) {
                Log::error('Error resizing image: ' . $e->getMessage());
            }
        }
    }
}
