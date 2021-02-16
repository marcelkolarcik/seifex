<?php

namespace App\Repository
;

use App\Delegation;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Role;
use App\Repository\DelegationRepository;
use App\User;
use App\Services\Company;
use App\Services\LocationNameOrId;

class NavigationRepository{
    
    public $role;
    public $delegation;
    
    public function __construct(Role $role,DelegationRepository $delegation ,Company $company)
    {
        $this->role         = $role;
        $this->delegation   = $delegation;
        $this->company      = $company;
    }
    
    
    public function owner_id(  $company_id = null )
    {
    
    
      
            if(session('delegator_cid_oid'))
            {
                if(is_array($company_id)) $company_id = array_values($company_id)[0];
            }
            
    //  dd($company_id,session('delegator_cid_oid'),session('delegator_cid_oid')[$company_id], !   $owner_id   =   session('delegator_cid_oid')[$company_id]  );
            
            if(  isset(session('delegator_cid_oid')[$company_id])    )
            {
                $owner_id   =   session('delegator_cid_oid')[$company_id];
               
            }
            elseif(  isset(session('delegator_cid_oid')[session('company_id')])    )
            {
                $owner_id   =   session('delegator_cid_oid')[session('company_id')];
            }
            else{
                $owner_id   =   \Auth::guard($this->role->get_guard())->user()->id;
            }
           // dd($owner_id,session('delegator_cid_oid'));
            return $owner_id;
       
        
    }
   
    public function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
    
    
   public function product_list_requests()
   {
       $product_list_requests = DB::table('product_list_requests')
           ->join($this->role->opposite_company_table(),'product_list_requests.'.$this->role->opposite_company_id(),'=',$this->role->opposite_company_table().'.id')
           ->where($this->role->company_id(),session()->get('company_id'))
           ->get(['*',$this->role->opposite_company_table().'.'.$this->role->opposite_company_name()])
           ->groupBy('department')
           ->toArray();
     
       return $product_list_requests;
   }
   
   public function user_names()
   {
       $user_names['buyer']     =   [];
       $user_names['seller']    =   [];
       $user_ids['buyer']       =   [];
       $user_ids['seller']      =   [];
       
       $product_list_requests = $this->product_list_requests();
      
       if($product_list_requests)
       {
           foreach($product_list_requests as  $seller_company_id    =>  $product_list_request)
           {
            
                $user_ids[$product_list_request[0]->guard][] = $product_list_request[0]->requester_user_id;
           }
          
                $user_names['buyer'] = DB::table('buyers')->whereIn('id',$user_ids['buyer'])->pluck('name','id')->toArray();
                $user_names['seller'] = DB::table('sellers')->whereIn('id',$user_ids['seller'])->pluck('name','id')->toArray();
       }
      
       return $user_names;
   }
}
