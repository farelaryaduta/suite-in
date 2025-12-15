<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsHotelOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'hotel_owner') {
            abort(403, 'Unauthorized access. Partner access required.');
        }

        return $next($request);
    }
}
