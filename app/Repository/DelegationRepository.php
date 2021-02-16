<?php

namespace App\Repository;

use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffDelegationEmail;
use Illuminate\Support\Str;
use App\User;
use App\Services\Role;
use App\Buyer;
use App\Seller;
use App\Jobs\StaffDelegationEmailJob;

class DelegationRepository{
   
    
    
    public function __construct(Role $role)
    {
        $this->role =   $role;
    }
    
    
    ////// STAFF LOGS IN INTO THE ACCOUNT => ACCEPTING DELEGATION
    public function accepted($owner)
    {
        $company_ids    =   [];
        
        if($delegations = DB::table('delegations')
            ->leftJoin('work_scopes','delegations.id','=','work_scopes.delegation_id')
            ->where('staff_email',\Auth::guard($this->role->get_guard())->user()->email)
            ->get(['delegations.*', 'work_scopes.details as scope'])->sortByDesc('delegated_at') )
        {
        
            foreach($delegations->toArray() as $delegation)
            {
                $companies = (object) [ $delegation->delegator_company_id   =>
                [
                    'id'             =>  $delegation->delegator_company_id,
                    $owner.'_company_name'   =>  $delegation->delegator_company_name,
                    'accepted_at'    =>  $delegation->accepted_at,
                    'role'           =>  $delegation->staff_role,
                    'position'       =>  $delegation->staff_position
                ]  ];
                
                
                $company_ids[$delegation->delegator_company_id] =  1;
                
                if($delegation->accepted_at == null)
                {
                    DB::table('delegations')
                        ->where('staff_email',\Auth::guard($this->role->get_guard())->user()->email)
                        ->update([
                            'staff_id'  =>  \Auth::guard($this->role->get_guard())->user()->id]);
                    
                   if($delegation->staff_position == 'staff')
                   {
                       DB::table('work_scopes')->
                           update([ 'staff_id' => \Auth::guard($this->role->get_guard())->user()->id ]);
                   }
                }
            }
           
           if($company_ids  ==   [])  return ['delegations' => [] ];
        
            return ['delegations' => $delegations, 'companies' => $companies,   'company_ids'  =>  array_keys($company_ids) ];
        }
        
    }
    ////// UNDELEGATE STAFF
    public function undelegate_staff($request,$old_staff_email,$staff_role,$position)
    {
       
        $details    =   $this->delegation_details($request,$staff_role,'un-delegated');
        
        dispatch(new StaffDelegationEmailJob($details,$staff_role,$old_staff_email));
        
        $company_id     =   session()->get('company_id');
       // dd($old_staff_email,$position,$staff_role,\Auth::guard($this->role->get_guard())->user()->role,$company_id,\Auth::guard($this->role->get_guard())->user()->id);
        DB::table('delegations')
            ->where('delegator_id',\Auth::guard($this->role->get_guard())->user()->id)
            ->where('delegator_company_id',$company_id)
            ->where('delegator_role',\Auth::guard($this->role->get_guard())->user()->role)
            ->where('staff_email',$old_staff_email)
            ->where( 'staff_role',$staff_role)
            ->where( 'staff_position',$position)
            ->update(['undelegated_at'=> date('Y-m-d H:i:s')]);
    
        $this->unset_duties($old_staff_email);
    }
    ////// DELEGATE STAFF
    public function delegate_staff($request,$company_id,$staff_role,$staff_email,$position,$staff_details = null)
    {
       
            $token      =   $this->token();
            $details    =   $this->delegation_details($request,$staff_role,'delegated',$staff_details);
            
            dispatch(new StaffDelegationEmailJob($details,$staff_role,$staff_email,$token));
            
           /*IF  WE ADDING NEW STAFF FROM NEW STAFF FORM position staff*/
        if($staff_details)
        {
           
            $company_name           =   $staff_details['company_name'];
            $staff_name             =   $staff_details['staff_name'];
            $staff_email            =   $staff_details['staff_email'];
        }
        /*IF  WE ADDING NEW STAFF FROM NEW COMPANY FORM position manager*/
        else
        {
            $delegator_company_name   =   explode('_',$staff_role)[0].'_company_name';
    
            if($staff_role  ==  'buyer_buyer' || $staff_role     ==   'seller_seller')
            {
        
                $delegated_email          =   explode('_',$staff_role)[0].'_email';
                $delegated_name           =   explode('_',$staff_role)[0].'_name';
            }
            else
            {
                $delegated_email          =   $staff_role.'_email';
                $delegated_name           =   $staff_role.'_name';
            }
            
            $company_name           =   $request->$delegator_company_name;
            $staff_name             =   $request->$delegated_name;
            $staff_email            =   $request->$delegated_email;
        }
      return    DB::table('delegations')->insertGetId([
                'delegator_id'              =>  \Auth::guard($this->role->get_guard())->user()->id,
                'delegator_company_id'      =>  $company_id,
                'delegator_role'            =>  \Auth::guard($this->role->get_guard())->user()->role,
                'delegator_email'           =>  \Auth::guard($this->role->get_guard())->user()->email,
                'delegator_company_name'    =>  $company_name,
                'staff_email'               =>  $staff_email,
                'staff_name'                =>  $staff_name,
                'staff_role'                =>  $staff_role,
                'staff_position'            =>  $position,
                'delegated_at'              =>  date('Y-m-d H:i:s'),
                'token'                     =>  $token,
            ]);
            
    }
    
    
    private function token()
    {
        return Str::random(60);
    }
    private function delegation_details($request,$staff_role,$delegated,$staff_details = null)
    {
        if($staff_role  ==  'buyer_buyer' || $staff_role     ==   'seller_seller')
        {
            $staff_name_desc    =   explode('_',$staff_role)[1].'_name';
            $staff_email_desc   =   explode('_',$staff_role)[1].'_email';
            
            
        }
        else
        {
            $staff_name_desc    =   $staff_role.'_name';
            $staff_email_desc   =   $staff_role.'_email';
            
            
        }
        
        $owner_desc             =   explode('_',$staff_role)[0].'_owner_name';
        $company_name_desc      =   explode('_',$staff_role)[0].'_company_name';
        
        $delegated_role         =   ucwords(explode('_',$staff_role)[1]);
      
        if($staff_details)
        {
            $owner_name             =    \Auth::guard($this->role->get_guard())->user()->name;
            $company_name           =   $staff_details['company_name'];
            $staff_name             =   $staff_details['staff_name'];
            $staff_email            =   $staff_details['staff_email'];
           
        }
        else
        {
                $owner_name             =    \Auth::guard($this->role->get_guard())->user()->name;
                $company_name           =   $request->$company_name_desc;
                $staff_name             =   $request->$staff_name_desc;
                $staff_email            =   $request->$staff_email_desc;
        }
    
       
        $message                =   __('You have received this email, because you were delegated a job of job_title by company_owner in company.',
            [
                'company_owner'  =>  \Auth::guard($this->role->get_guard())->user()->name,
                'job_title'      =>  explode('_',$staff_role)[1],
                'delegated'      =>  $delegated,
                'company'        =>  $company_name
            ]);
          
      
        return [
            'staff_name'     =>  $staff_name,
            'owner_name'     =>  $owner_name,
            'company_name'   =>  $company_name,
            'delegated_role' =>  $delegated_role,
            'staff_email'    =>  $staff_email,
            'message'        =>  $message];
    }
    private function unset_duties($staff_email)
    {
       
        $Staff_model = ucwords($this->role->get_guard());
    
        if($Staff_model == 'Buyer')
        {
            if( Buyer::where('email',$staff_email)->first())
            {
                $staff          =   Buyer::where('email',$staff_email)->get(['id','role','duties'])->first()->toArray();
                $company_id     =   session()->get('company_id');
                $staff_id       =   $staff['id'];
                $staff_role     =   $staff['role'];
                $staff_duties   =   json_decode($staff['duties'],true);
            
                unset($staff_duties[$company_id][$staff_id][$staff_role]);
            
                Buyer::where('email',$staff_email)->update(['duties' =>  json_encode($staff_duties)]);
            }
        }
        elseif($Staff_model == 'Seller')
        {
            if( Seller::where('email',$staff_email)->first())
            {
                $staff          =   Seller::where('email',$staff_email)->get(['id','role','duties'])->first()->toArray();
                $company_id     =   session()->get('company_id');
                $staff_id       =   $staff['id'];
                $staff_role     =   $staff['role'];
                $staff_duties   =   json_decode($staff['duties'],true);
            
                unset($staff_duties[$company_id][$staff_id][$staff_role]);
            
                Seller::where('email',$staff_email)->update(['duties' =>  json_encode($staff_duties)]);
            }
        }
        
        
        
        
    }
}
