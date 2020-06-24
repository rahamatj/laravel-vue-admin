<?php

namespace App\Otp\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class ActivateGoogle2fa
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
        $user = Auth::user();

        if ($user->is_google2fa_activated || $user->otp_type != 'google2fa')
            return response()->json([
                'message' => 'Forbidden.'
            ], 403);

        return $next($request);
    }
}
