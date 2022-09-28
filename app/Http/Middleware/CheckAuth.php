<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

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
        if(session()->get('url_atual')) {
            session()->put('url_anterior', session()->get('url_atual'));
        } 
        session()->put('url_atual', URL::current());

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
