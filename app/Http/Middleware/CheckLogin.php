<?php

namespace App\Http\Middleware;

use Closure;
use Route;
class CheckLogin
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
        if(!session('isLogin') && !in_array(Route::currentRouteName(),['loginCallback','login'])){
            return redirect('login');
        }
        return $next($request);
    }
}
