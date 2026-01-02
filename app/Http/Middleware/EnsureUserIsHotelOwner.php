<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsHotelOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use partner guard for authentication
        if (!Auth::guard('partner')->check()) {
            return redirect()->route('partner.login');
        }

        $user = Auth::guard('partner')->user();

        // Double-check role for security
        if ($user->role !== 'hotel_owner') {
            Auth::guard('partner')->logout();
            $request->session()->invalidate();
            abort(403, 'Unauthorized access. Partner privileges required.');
        }

        return $next($request);
    }
}
