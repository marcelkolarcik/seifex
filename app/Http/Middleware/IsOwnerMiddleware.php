<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsOwnerMiddleware
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
        if(Auth::guard('buyer')->check())       {
            $owner    =   'buyer';
        }
        elseif(Auth::guard('seller')->check())  {
            $owner    =   'seller';
        }
        else{
            abort(401, 'Unauthorized action.');
        }
        
        $company_id =   explode('/',$request->getRequestUri())[sizeof( explode('/',$request->getRequestUri()))  - 1];
    
        if(in_array($company_id, session($owner.'_company_ids'))
                || in_array(session()->get('company_id'), session($owner.'_company_ids')))
        return $next($request);
      
        else abort(401, 'Unauthorized action.');
        
    }
}
