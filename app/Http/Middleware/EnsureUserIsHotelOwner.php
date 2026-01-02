<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsHotelOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'hotel_owner') {
            // If user is logged in but not hotel owner
            if ($user) {
                // Redirect admins to admin dashboard
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'You are an admin. Redirected to Admin Dashboard.');
                }
                // Redirect customers to home
                return redirect()->route('home')
                    ->with('error', 'You do not have partner access.');
            }
            // Not logged in - redirect to partner login
            return redirect()->route('partner.login');
        }

        return $next($request);
    }
}
