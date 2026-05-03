<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('img_url')) {
    function img_url(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : Storage::url($path);
    }
}
