<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo('/');
        $middleware->redirectGuestsTo('/login');
        $middleware->validateCsrfTokens(except: [
            'payment/notification',
        ]);
        
        $middleware->alias([
            // admin.owner: Allow BOTH admin AND hotel_owner (for shared resources)
            // Use with caution - most routes should use 'admin' OR 'hotel.owner', not both
            'admin.owner' => \App\Http\Middleware\EnsureUserIsAdminOrOwner::class,
            'hotel.owner' => \App\Http\Middleware\EnsureUserIsHotelOwner::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
