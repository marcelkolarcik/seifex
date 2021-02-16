<?php

namespace App\Http\Middleware\Roles;

use Closure;

class SellerSellerMiddleware
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
        if ($request->user()->role != 'seller_seller') {
            return redirect('/seller/login');
        }
        return $next($request);
    }
}
