<?php
//
// Require user to relogin if user.logout attribute is true
//
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mime\Encoder\EncoderInterface;

class RefreshUser
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
        if($this->refreshReqired($user, $request)) {
            $request->session()->flash('refer_page', $request->fullUrl());
            $url = config('app.url').'/oauth_callback/profile';
            return redirect($url);
        }
        return $next($request);
    }


    protected function refreshReqired($user, $request) {
        return $user &&
            $user->refresh_required &&
            !$request->expectsJson() &&
            $request->method() == 'GET';
    }


}
