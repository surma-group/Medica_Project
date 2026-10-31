<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EmployeeMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {

            // Partner Routes (Prefix: partner)
            Route::middleware('web')
                ->prefix('partner')
                ->group(base_path('routes/partner.php'));

            // Admin Routes (Prefix: em_admin)
            Route::middleware('web')
                ->prefix('em_admin')
                ->group(base_path('routes/admin.php'));

            // Employee Routes
            Route::middleware('web')
                ->prefix('employee')
                ->group(base_path('routes/employee.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'employee' => EmployeeMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {

            if ($request->is('em_admin*')) {
                return route('admin.login');
            }

            if ($request->is('partner*')) {
                return route('login'); // or partner.login if you create separate login
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
