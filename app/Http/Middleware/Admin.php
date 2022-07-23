<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user()->isAdmin()) {
            return $next($request);
        } else {
            $error_data = [
                'message' => 'Unauthorized',
                'status' => false,
                'data' => NULL
            ];
            return response()->json($error_data, 401);
        }
    }
}
