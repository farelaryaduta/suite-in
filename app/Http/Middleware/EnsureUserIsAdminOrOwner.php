<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdminOrOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized access. Please login first.');
        }

        if (!$user->isAdmin() && !$user->isHotelOwner()) {
            abort(403, 'Unauthorized access. Admin or Hotel Owner access required. Your role: ' . ($user->role ?? 'none'));
        }

        return $next($request);
    }
}

