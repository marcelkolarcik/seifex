<?php

namespace App\Http\Middleware\Roles;

use Closure;

class BuyerOwnerMiddleware
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
       
        if (\Auth::guard('buyer')->user()->role != 'buyer_owner') {
            return redirect('/buyer/login');
        }
        return $next($request);
    }
}
