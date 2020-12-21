<?php

namespace App\Http\Middleware;

use Closure;

class CheckAuth
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
        if (!auth()->check()) {
            
            if(preg_match('{gaviao}', $request->path())){
                return redirect('gaviao');
            }else{
                return redirect('login');
            }
            
        }
        return $next($request);
    }
}
