<?php

namespace App\Otp\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Google2faIsNotActivated
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

        if ($user->otp_type == 'google2fa' && ! $user->is_google2fa_activated)
            return response()->json([
                'message' => 'Forbidden.'
            ], 403);

        return $next($request);
    }
}
