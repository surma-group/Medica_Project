<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        // ইউজার লগইন থাকা অবস্থায় যদি সে CUSTOMER না হয়
        if (Auth::check() && Auth::user()->type != USER_TYPE_CUSTOMER) {
            return redirect()->route('partner.dashboard');
        }

        return $next($request);
    }
}