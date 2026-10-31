<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Partner;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePartnerIsAgent
{
    public function handle(Request $request, Closure $next): Response
    {
        $partner = Partner::where('user_id', Auth::id())->first();

        if (!$partner || (int) $partner->type !== USER_TYPE_AGENT) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}