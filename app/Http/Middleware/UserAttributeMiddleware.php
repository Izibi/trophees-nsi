<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAttributeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$attrs)
    {
        $user = $request->user();
        if(!$user) {
            return redirect('/');
        }
        foreach($attrs as $attr) {
            if(!$user->$attr) {
                return redirect('/');
            }
        }
        return $next($request);
    }
}
