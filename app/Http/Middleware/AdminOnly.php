<?php

namespace App\Http\Middleware;

use Closure;
use App\Constants\UserType;
use Illuminate\Support\Facades\Log;

class AdminOnly
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
        $user = auth()->user();

        if (!$user || $user->role !== UserType::ADMIN) {
            return response()->json(['msg' => '権限がありません'], 403);
        }
        return $next($request);
    }
}
