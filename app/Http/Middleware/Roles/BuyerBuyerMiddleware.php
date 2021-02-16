<?php

namespace App\Http\Middleware\Roles;

use Closure;

class BuyerBuyerMiddleware
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
        if ($request->user()->role != 'buyer_buyer') {
            return redirect('/buyer/login');
        }
        return $next($request);
    }
}
