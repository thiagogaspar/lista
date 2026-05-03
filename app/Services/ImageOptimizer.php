<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    public function convertToWebp(string $path, ?string $disk = null): ?string
    {
        $disk ??= config('filesystems.default');
        $fullPath = Storage::disk($disk)->path($path);

        if (! file_exists($fullPath)) {
            return null;
        }

        $mime = mime_content_type($fullPath);
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return null;
        }

        $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $fullPath);
        $webpRelative = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $path);

        if (file_exists($webpPath)) {
            return $webpRelative;
        }

        try {
            $img = match ($mime) {
                'image/jpeg' => imagecreatefromjpeg($fullPath),
                'image/png' => imagecreatefrompng($fullPath),
                'image/gif' => imagecreatefromgif($fullPath),
                'image/webp' => imagecreatefromwebp($fullPath),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }

        if (! $img) {
            return null;
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        $quality = $mime === 'image/png' ? 80 : 85;
        imagewebp($img, $webpPath, $quality);
        imagedestroy($img);

        return $webpRelative;
    }

    public function handleUpload(UploadedFile $file, string $directory, ?string $disk = null): array
    {
        $originalPath = $file->store($directory, $disk ?? config('filesystems.default'));

        $webpPath = null;
        try {
            $webpPath = $this->convertToWebp($originalPath, $disk);
        } catch (\Throwable $e) {
            report($e);
        }

        return [
            'original' => $originalPath,
            'webp' => $webpPath,
        ];
    }
}
