<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            //如果已登入卻進入了被訪客保護的連結
            //根據guard的不同 轉移到不同的網址
            switch ($guard) {
                case 'admins':
                    $redirect_name = 'admin.home';
                    break;
                
                default:
                    $redirect_name = 'home';
                    break;
            }

            return redirect(route($redirect_name));
        }

        return $next($request);
    }
}
