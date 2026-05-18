<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            Log::warning('Admin access denied', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
            ]);

            abort(403, sprintf(
                'Access denied. You are logged in as %s (role: %s). ' .
                'Please <a href="/logout">logout</a> and use admin@lista.site with password 1234.',
                $user->email,
                $user->role
            ));
        }

        if ($role === 'editor' && ! $user->isEditor()) {
            Log::warning('Editor access denied', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
            ]);

            abort(403, 'Editor access required.');
        }

        return $next($request);
    }
}
