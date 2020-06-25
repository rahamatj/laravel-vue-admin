<?php

namespace App\Otp\Middleware;

use App\Otp\Exceptions\FingerprintHeaderRequiredException;
use Illuminate\Support\Facades\Auth;
use App\Otp\Otp;
use Closure;

class VerifyOtpAtLogin
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
        if (! $fingerprint = $request->header('fingerprint'))
            throw new FingerprintHeaderRequiredException('Fingerprint header is required.');

        if (! Auth::user()->is_otp_verification_enabled_at_login
            || Otp::isVerifiedAtLogin($fingerprint))
            return response()->json([
               'message' => 'Forbidden.'
            ], 403);

        return $next($request);
    }
}
