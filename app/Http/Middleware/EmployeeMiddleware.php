<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->type != USER_TYPE_EMPLOYEE) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'You are not authorized to access the employee panel.'
                ]);
        }

        return $next($request);
    }
}