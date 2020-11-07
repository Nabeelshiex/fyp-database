<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Response;

class CustomAuth
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
        if ($request->token) {
            $user = User::where('remember_token', $request->token)->first();
            if ($user) {
                return $next($request);
            }
        }

        return Response::json(['message' => 'You do not have access'], 401);
    }
}
