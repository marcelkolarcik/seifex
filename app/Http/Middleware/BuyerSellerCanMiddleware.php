<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BuyerSellerCanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$resource)
    {
        if(Auth::guard('buyer')->check())       {
            $owner    =   'buyer';
            if(Auth::guard('buyer')->user()->role === 'buyer_owner') return $next($request);
        }
        elseif(Auth::guard('seller')->check())  {
            $owner    =   'seller';
            if(Auth::guard('seller')->user()->role === 'seller_owner') return $next($request);
        }
        
        
    
        $staff_role     =   \Auth::guard($owner)->user()->role;
        $staff_duties   =   json_decode(\Auth::guard($owner)->user()->duties,true);
     
        if( ! isset (   $staff_duties[session('company_id')][\Auth::guard($owner)->user()->id][$staff_role][$resource]   )  )
        {
           
            abort(403, 'Unauthorized action.');
        }
       
        return $next($request);
    }
}
