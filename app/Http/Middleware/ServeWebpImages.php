<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ServeWebpImages
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->accepts('image/webp')) {
            return $response;
        }

        $path = $request->path();
        if (! preg_match('/\.(jpg|jpeg|png|gif)$/i', $path)) {
            return $response;
        }

        // Check disk
        $disks = ['public', 'local'];
        foreach ($disks as $disk) {
            $webp = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $path);
            if (Storage::disk($disk)->exists($webp)) {
                $url = Storage::disk($disk)->url($webp);

                return redirect($url, 301);
            }
        }

        return $response;
    }
}
