<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuth
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
        if(Auth::check()){
            return $next($request);
        }
        elseif($request->email=="rajmalhotra.sant@gmail.com" && $request->password=="raj@654321"){
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                return $next($request);
            }
            return redirect()->route('loginView')->with('message', 'Invalid credentials!');
        }
        else{
            return redirect()->route('loginView');
        }
    }
}
