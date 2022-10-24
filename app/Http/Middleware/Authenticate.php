<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class Authenticate {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $accessToken = Cookie::get('accessToken');
        if(!$accessToken) return redirect()->route('login');

        return $next($request);
    }
}
