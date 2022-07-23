<?php

namespace App\Http\Middleware;

use App\Domain\Helpers\EnvService;
use Closure;
use Illuminate\Support\Facades\Auth;

class Testing
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if(EnvService::isProd()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401); 
        }

        return $next($request);
    }
}
