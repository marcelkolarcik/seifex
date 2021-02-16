<?php

namespace App\Http\Middleware;


use Closure;

class BuyerCanMiddleware
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
        $buyer  =   \Auth::guard('buyer')->user();
        
        if($buyer->role === 'buyer_owner') return $next($request);
        
        
        $buyer_duties   =   json_decode($buyer->duties,true);
    
        if( ! isset (   $buyer_duties[session('company_id')][$buyer->id][$buyer->role][$resource]   )  )
        {
            return redirect('/');
        }
        
        return $next($request);
    }
}
