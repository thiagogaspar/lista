<?php

use App\Services\ImageOptimizer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:optimize {disk=public}', function ($disk) {
    $directories = ['bands', 'bands/hero', 'bands/gallery', 'artists', 'artists/hero', 'artists/gallery', 'albums', 'labels', 'blog'];
    $total = 0;
    foreach ($directories as $dir) {
        if (! Storage::disk($disk)->exists($dir)) {
            continue;
        }
        foreach (Storage::disk($disk)->files($dir) as $file) {
            if (! preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                continue;
            }
            $webp = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $file);
            if (Storage::disk($disk)->exists($webp)) {
                continue;
            }
            if ((new ImageOptimizer)->convertToWebp($file, $disk)) {
                $total++;
                $this->line("  Converted: {$file}");
            }
        }
    }
    $this->info("Done. {$total} images converted to WebP.");
})->purpose('Convert all images to WebP format');
