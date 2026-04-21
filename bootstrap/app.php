<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Employee Routes (Prefix: employee)
            Route::middleware('web')
                ->prefix('employee')
                ->group(base_path('routes/users.php'));

            // Admin Routes (Prefix: em_admin)
            Route::middleware('web')
                ->prefix('em_admin')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your 'admin' alias here
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            // If the URL starts with em_admin, redirect to the admin login
            if ($request->is('em_admin*')) {
                return route('admin.login');
            }

            // Default redirection for regular users
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
