<?php

namespace App\Http\Middleware;


use Closure;

class AdminCanMiddleware
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
       
        $admin  =   \Auth::guard('admin')->user();
        
        if(\Auth::guard('owner')->check()) return $next($request);
    
    
        $admin_duties   =   json_decode($admin->duties,true);
        
        if( ! in_array($resource,$admin_duties)  )
        {
            return redirect('/');
        }
        
        return $next($request);
    }
}
