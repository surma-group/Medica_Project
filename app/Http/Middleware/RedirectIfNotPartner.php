<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotPartner
{
    public function handle(Request $request, Closure $next): Response
    {
        // ইউজার যদি DEALER বা AGENT কোনোটিই না হয়
        if (Auth::check()) {
            $type = Auth::user()->type;
            if ($type != USER_TYPE_DEALER && $type != USER_TYPE_AGENT) {
                return redirect()->route('profile');
            }
        }

        return $next($request);
    }
}