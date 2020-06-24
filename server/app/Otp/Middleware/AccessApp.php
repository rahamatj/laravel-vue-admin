<?php

namespace App\Otp\Middleware;

use App\Otp\Exceptions\ClientFingerprintRequiredException;
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
        if (! $request->has('fingerprint'))
            throw new ClientFingerprintRequiredException('Client fingerprint is required.');

        if (Auth::user()->is_otp_verification_enabled_at_login
            && ! Otp::hasBeenVerifiedAtLogin($request->fingerprint))
            return response()->json([
                'message' => 'Forbidden.'
            ], 403);

        return $next($request);
    }
}
