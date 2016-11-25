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
        if(!session('isLogin') && 'login' != Route::currentRouteName()){
            //return redirect('login');
            var_dump(session('isLogin'));
            var_dump(Route::currentRouteName());
        }
        return $next($request);
    }
}
