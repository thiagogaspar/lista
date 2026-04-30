<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('filament.admin.auth.login');
        }

        if ($role === 'admin' && ! $user->isAdmin()) {
            abort(403, 'Admin access required.');
        }

        if ($role === 'editor' && ! $user->isEditor()) {
            abort(403, 'Editor access required.');
        }

        return $next($request);
    }
}
