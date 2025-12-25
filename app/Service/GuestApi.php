<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class GuestApi
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
       if (auth('api')->check()) {
            return response()->json([], 403);
        }
        return $next($request);
    }
}
