<?php

namespace App\Http\Middleware;

use App\Exceptions\FingerprintHeaderRequiredException;
use Closure;

class FingerprintHeaderRequired
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
        if (! $request->header('fingerprint'))
            throw new FingerprintHeaderRequiredException('Fingerprint header is required.');

        return $next($request);
    }
}
