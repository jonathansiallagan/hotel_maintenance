<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->redirectUsersTo(function () {
            $user = Auth::user();

            if (!$user) {
                return '/login';
            }

            return match ($user->role) {
                'admin'      => route('admin.dashboard'),
                'technician' => route('technician.dashboard'),
                'staff'      => route('staff.dashboard'),
                default      => '/dashboard',
            };
        });

        $middleware->trustProxies(at: '*');
        // -----------------------------

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
