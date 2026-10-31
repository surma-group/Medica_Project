<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (
            auth()->user()->type == USER_TYPE_DEALER ||
            auth()->user()->type == USER_TYPE_AGENT
        ) {
            return $next($request);
        }

        abort(403);
    }
}
