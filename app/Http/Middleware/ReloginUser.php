<?php
//
// Require user to relogin if user.logout attribute is true
//
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReloginUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if($user && $user->relogin_required) {
            $user->relogin_required = false;
            $user->save();
            Auth::logout();
            if($request->expectsJson()) {
                $msg = [
                    'error' => 'Unauthenticated.'
                ];
                return response()->json($msg, 401);
            } else {
                return redirect('/');
            }
        }
        return $next($request);
    }
}
