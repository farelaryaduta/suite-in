<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            // If user is logged in but not admin, log them out of admin context
            if ($user) {
                // Redirect hotel owners to partner dashboard
                if ($user->isHotelOwner()) {
                    return redirect()->route('partner.dashboard')
                        ->with('error', 'You do not have admin access. Redirected to Partner Dashboard.');
                }
                // Redirect customers to home
                return redirect()->route('home')
                    ->with('error', 'You do not have admin access.');
            }
            // Not logged in - redirect to admin login
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
