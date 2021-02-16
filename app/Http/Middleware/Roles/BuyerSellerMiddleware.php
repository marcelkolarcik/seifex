<?php

namespace App\Http\Middleware\Roles;

use Closure;

class BuyerSellerMiddleware
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
       
        if (\Auth::guard('buyer')->user() || \Auth::guard('seller')->user())   return $next($request);
        
        return redirect('/');
       
        
        
    }
}
