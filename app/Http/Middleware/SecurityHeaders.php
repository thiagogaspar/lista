<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        if (app()->environment('local')) {
            $csp = "default-src 'self'; ".
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:* http://[::1]:*; ".
                   "style-src 'self' 'unsafe-inline' https://fonts.bunny.net http://localhost:*; ".
                   "img-src 'self' data: https://picsum.photos https://*.picsum.photos https://via.placeholder.com http://localhost:*; ".
                   "font-src 'self' https://fonts.bunny.net; ".
                   "connect-src 'self' ws://localhost:* ws://[::1]:* http://localhost:* https://fonts.bunny.net; ".
                   "frame-src 'none'; ".
                   "object-src 'none'; ".
                   "base-uri 'self'; ".
                   "form-action 'self'";
        } else {
            $origin = $request->getSchemeAndHttpHost();
            $csp = "default-src 'self'; ".
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' {$origin}; ".
                   "style-src 'self' 'unsafe-inline' https://fonts.bunny.net {$origin}; ".
                    "img-src 'self' data: https://picsum.photos https://*.picsum.photos https://*.wikimedia.org {$origin}; ".
                   "font-src 'self' https://fonts.bunny.net; ".
                   "connect-src 'self' https://fonts.bunny.net {$origin}; ".
                   "frame-src 'none'; ".
                   "object-src 'none'; ".
                   "base-uri 'self'; ".
                   "form-action 'self'";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
