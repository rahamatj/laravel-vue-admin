<?php

namespace App\Otp\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Otp\Otp;
use Closure;

class AccessApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->is_otp_verification_enabled_at_login
            && ! Otp::isVerifiedAtLogin($request->header('Fingerprint')))
            return response()->json([
                'message' => 'Forbidden.'
            ], 403);

        return $next($request);
    }
}
