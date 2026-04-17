<?php

namespace App\Http\Middleware;

use Closure;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('url_atual')) {
            session()->put('url_anterior', session('url_atual'));
        }

        session()->put('url_atual', $request->fullUrl());

        if (!auth()->check()) {
            $host = $request->getHost();

            if (strpos($host, 'gaviao') !== false) {
                return redirect('gaviao');
            }

            return redirect('login');
        }

        return $next($request);
    }
}
