<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserSuppend
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
        if (Auth::check()) {
            if (Auth::user()->block == 1) {
                Auth::user()->oAuthAccessToken()->delete();
                return response()->json([
                    'message' => 'Your Account is suspended!',
                    'error' => [
                        'message' => 'Your Account is suspended'
                    ]
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
