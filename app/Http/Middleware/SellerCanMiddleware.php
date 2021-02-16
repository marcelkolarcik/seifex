<?php

namespace App\Http\Middleware;

use Closure;

class SellerCanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $resource)
    {
        $seller  =   \Auth::guard('seller')->user();
        
        if($seller->role === 'seller_owner') return $next($request);
        
        $seller_duties   =   json_decode($seller->duties,true);
       
        if( ! isset (   $seller_duties[session('company_id')][$seller->id][$seller->role][$resource]   )  )
        {
            return redirect('/');
        }
        
        return $next($request);
    }
}
