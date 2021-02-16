<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 12/20/2018
 * Time: 9:26 PM
 */

namespace App\Services;


use function Couchbase\defaultDecoder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Services\Role;

class Company
{
    public $role;
    public function __construct(Role $role)
    {
        $this->role =   $role;
    }
    
//    public static function company_id($company_id)
//    {
//        return  session(['company_id'    =>  $company_id]);
//    }
    public function update($group)
    {
        /*ON ANY UPDATE, DELETE OR ADD WE WILL PULL  COMPANIES FROM THE SESSION*/
        session()->pull('owner_companies');
        
        if(is_array($group))
            foreach($group as $member)
            {
                session()->pull($member);
            }
            
       else
        session()->pull($group);
    }
    static function static_update($group)
    {
       
        /*ON ANY UPDATE, DELETE OR ADD WE WILL PULL  COMPANIES FROM THE SESSION*/
        session()->pull('owner_companies');
        
        if(is_array($group))
            foreach($group as $member)
            {
                session()->pull($member);
            }
        
        else{
              
                 session()->pull($group);
        }
        
    }
    static function static_for($type)
    {
        if( session()->has('owner_companies'))  return session()->get('owner_companies');
        /*ON ANY UPDATE, DELETE OR ADD WE WILL PULL  COMPANIES FROM THE SESSION*/
        $companies =Role::guard().'_companies';
    
        $owner_companies  =    Company::$companies($type);
    
        session([Role::guard().'_company_ids' => array_keys(  $owner_companies)]);
        session(['owner_companies' => $owner_companies]);
       
        return $owner_companies ;
    }
    public function for($type)
    {
       if( session()->has('owner_companies'))  return session()->get('owner_companies');
    
       
          /*buyer_companies or seller_companies*/
          $companies = Role::guard().'_companies';

          $owner_companies  =   $this->$companies($type);

          session([Role::guard().'_company_ids' => array_keys(  $owner_companies)]);
          session(['owner_companies' => $owner_companies]);
    
          return $owner_companies ;
     
      
    }
    
    
    static function buyer_companies($for)
    {
        $owner_companies = [];
        /*UPON UPDATING STAFF DETAILS, STAFF DUTIES, STAFF SCOPE WE WILL PULL STAFF FROM THE SESSION AND WE WILL UPDATE OWNER COMPANIES THEN..*/
        $staff                  =  session()->has('staff') ? session()->get('staff') : Company::buyer_staff($for);
       // $staff                  =  Company::buyer_staff($for);
    
        /*UPON ORDERING WE WILL PULL STATISTICS FROM THE SESSION AND WE WILL UPDATE OWNER COMPANIES THEN..*/
        $statistics                  =  session()->has('statistics') ? session()->get('statistics') : Company::statistics($for);
       // $statistics                  =   Company::statistics($for);
    
        /*UPON UPDATING PRODUCT LIST REQUESTS PULL SELLER COMPANIES FROM THE SESSION AND WE WILL UPDATE OWNER COMPANIES THEN..*/
       $seller_companies        =  session()->has('seller_companies') ? session()->get('seller_companies') : Company::seller_companies_for_buyer($for);
       //$seller_companies        =Company::seller_companies_for_buyer($for);
            /*UPON UPDATING PRICE LIST REQUESTS PULL PRICE LISTS FROM THE SESSION AND WE WILL UPDATE OWNER COMPANIES THEN..*/
        $product_lists            =  session()->has('product_lists') ? session()->get('product_lists') : Company::product_lists($for);
        //$product_lists            =  Company::product_lists($for);
    
        /*UPON UPDATING PRICE LIST  ON SELLER SIDE PULL PRICE LISTS FROM THE SESSION AND WE WILL UPDATE OWNER COMPANIES THEN..*/
       $price_lists            =  session()->has('price_lists') ? session()->get('price_lists') : Company::price_lists_for_buyer($for);
     
     // $price_lists            =  Company::price_lists_for_buyer($for);
        /*UPON UPDATING SELLER COMPANY PULL SELLER COMPANY FROM THE SESSION AND WE WILL UPDATE OWNER COMPANIES THEN..*/
      
      $companies              =  session()->has('buyer_companies') ? session()->get('buyer_companies') : Company::companies_for_buyer($for);
     // $companies     =Company::companies_for_buyer($for);
        
      
        foreach($companies as $key => $company)
        {
          
            $owner_companies[$company->buyer_company_id] = (object)
            [
                'buyer_company_name'         =>      $company->buyer_company_name,
                'id'                         =>      $company->buyer_company_id,
                'buyer_id'                   =>      $company->buyer_id,
                
                'currencies'                 =>      json_decode( $company->currencies,true) ,
                'languages'                  =>      json_decode( $company->languages,true) ,
                
                'country'                    =>      $company->country,
                'county'                     =>      $company->county,
                'county_l4'                  =>      $company->county_l4,
                
                'buyer_owner_name'           =>      $company->buyer_owner_name,
                'buyer_owner_email'          =>      $company->buyer_owner_email,
                'buyer_owner_phone_number'   =>      $company->buyer_owner_phone_number,
    
                'buyer_name'                 =>      $company->buyer_name,
                'buyer_email'                =>      $company->buyer_email,
                'buyer_phone_number'         =>      $company->buyer_phone_number,
    
                'buyer_accountant_name'      =>      $company->buyer_accountant_name,
                'buyer_accountant_email'     =>      $company->buyer_accountant_email,
            'buyer_accountant_phone_number'  =>      $company->buyer_accountant_phone_number,
                
                'VAT_number'                 =>      $company->VAT_number,
                'address'                    =>      json_decode( $company->address,true) ,
             
                
                'logged_in_staff'            =>      isset($staff[$company->buyer_company_id]['logged_in_staff'])
                    ? $staff[$company->buyer_company_id]['logged_in_staff'] : null,
                'product_lists'              =>      isset($product_lists[ $company->buyer_company_id])
                    ? $product_lists[ $company->buyer_company_id] : null,
                'price_lists'                =>      isset($price_lists[ $company->buyer_company_id])
                    ? $price_lists[ $company->buyer_company_id] : null ,
                'staff'                      =>      isset($staff[$company->buyer_company_id])
                    ? $staff[$company->buyer_company_id] : null,
                'seller_companies'           =>      isset($seller_companies[$company->buyer_company_id])
                    ? $seller_companies[$company->buyer_company_id] : null,
                'order_statistics'           =>      isset($statistics[$company->buyer_company_id])
                    ?  $statistics[$company->buyer_company_id] : null,
                
                /*price_lists extended is there twice , need to revisit*/
                'seller_price_lists_extended'=>      isset($seller_companies[ 'seller_price_lists'][$company->buyer_company_id])
                    ? $seller_companies[ 'seller_price_lists'][$company->buyer_company_id]:null,
                'sellers_payment_frequency'   =>    isset($price_lists['payment_frequency'][$company->buyer_company_id]) ?
                    $price_lists['payment_frequency'][$company->buyer_company_id]
                    :   null  ,
                'sellers_delivery_days'       =>    isset( $price_lists['delivery_days'][$company->buyer_company_id]) ?
                    $price_lists['delivery_days'][$company->buyer_company_id] : null ,
               
            ];
            
            
        }
 
        return $owner_companies;
    }
    /* for buyer company*/
    static function buyer_staff($for)
    {
        $query =  DB::table('buyer_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','buyer_companies.id')
            ->leftJoin('work_scopes','work_scopes.delegation_id','=','delegations.id')
            ->leftJoin('buyers','buyers.id','=','delegations.staff_id')
            ->where('delegations.delegator_role','buyer_owner');
    
        if($for == 'staff')
        {
            $query->whereIn('buyer_companies.id',  DB::table('delegations')
            
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('buyers','buyers.email','=','delegations.staff_email')
                ->join('buyer_companies', 'buyer_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_email', \Auth::guard('buyer')->user()->email)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('buyer_companies.buyer_id', \Auth::guard('buyer')->user()->id);
        }
    
        $employees =  $query->get([
            'buyer_companies.country',
            'buyer_companies.county',
            'buyer_companies.county_l4',
            'buyer_companies.buyer_company_name',
            'buyer_companies.id as buyer_company_id',
            'buyer_companies.buyer_email',
            'buyer_companies.buyer_phone_number',
            'buyer_companies.buyer_accountant_email',
            'buyer_companies.buyer_accountant_phone_number',
            'buyer_companies.buyer_owner_email',
            'buyer_companies.currencies',
            'buyer_companies.languages',
          
        
            'delegations.staff_id',
            'delegations.id as delegation_id',
            'delegations.delegator_company_name',
            'delegations.staff_name',
            'delegations.staff_role',
            'delegations.staff_position',
            'delegations.staff_email',
            'delegations.delegated_at',
            'delegations.accepted_at',
            'delegations.undelegated_at',
        
            'work_scopes.details as scope',
            'work_scopes.staff_phone_number',
        
            'buyers.duties as staff_duties',
    
        ])
            ->toArray();
       
  
        $staff=[];
        $logged_in_staff = null;
   
        foreach($employees as $key=>$employee)
        {
            /*BUYER STAFF*/
        
          
            
                if(explode('_',$employee->staff_role)[1] == 'accountant' && $employee->staff_position ==  'manager')    {
                    $phone_number = 'buyer_accountant_phone_number';
                    $email        =   'buyer_accountant_email';
                }
                elseif(explode('_',$employee->staff_role)[1] == 'buyer'  && $employee->staff_position ==  'manager')   {
                    $phone_number = 'buyer_phone_number';
                    $email        =   'buyer_email';
                }
               
                elseif( $employee->staff_position ==  'staff')   {
                    $phone_number = 'staff_phone_number';
                    $email        =   'staff_email';
                }
                if($for ==  'staff' && $employee->staff_email == \Auth::guard('buyer')->user()->email)
                {
                    $logged_in_staff[$employee->buyer_company_id] = [
                        'delegated_at'          =>      $employee->delegated_at,
                        'undelegated_at'        =>      $employee->undelegated_at,
                        'accepted_at'           =>      $employee->accepted_at,
                        'role'                  =>      $employee->staff_role,
                        'staff_position'        =>      $employee->staff_position,
                        'staff_id'              =>      $employee->staff_id,
                        'staff_email'           =>      $employee->staff_email,
                        'company_id'            =>      $employee->buyer_company_id,
                        'scope'                 =>      json_decode($employee->scope,true),
                        'duties'                =>      json_decode($employee->staff_duties,true),
                    ];
                }
            
                $staff[$employee->buyer_company_id][$employee->staff_role][ hash('adler32',$employee->staff_role.$employee->staff_position.$employee->staff_id) ]
                    = [
                    'delegated_at'          =>      $employee->delegated_at,
                    'undelegated_at'        =>      $employee->undelegated_at,
                    'accepted_at'           =>      $employee->accepted_at,
                    'scope'                 =>      json_decode($employee->scope,true),
                    'duties'                =>      json_decode($employee->staff_duties,true),
                    'staff_name'            =>      $employee->staff_name,
                    'role'                  =>      $employee->staff_role,
                    'staff_position'        =>      $employee->staff_position,
                    'staff_id'              =>      $employee->staff_id,
                    'delegation_id'         =>      $employee->delegation_id,
                    'delegator_company_name'=>      $employee->delegator_company_name,
                    'phone_number'          =>      $employee->$phone_number,
                    'email'                 =>      $employee->$email,
            
                ];


                $staff[$employee->buyer_company_id] ['logged_in_staff']  =
                    !$logged_in_staff ? null : $logged_in_staff[$employee->buyer_company_id];
                $staff[$employee->buyer_company_id] ['staff_ids'] [$employee->staff_role][$employee->staff_id] = 1  ;
           
        
           
        }
    
     
        session()->put(  'staff'  ,  $staff  );
        return $staff;
    }
    static function seller_companies_for_buyer($for)
    {
        $seller_companies = [];
        $query =  DB::table('buyer_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','buyer_companies.id')
            ->leftJoin('product_list_requests','product_list_requests.buyer_company_id','=','buyer_companies.id')
            ->leftJoin('seller_companies','seller_companies.id','=','product_list_requests.seller_company_id')
            ->leftJoin('price_lists_extended','price_lists_extended.seller_company_id','=','product_list_requests.seller_company_id');
        
        if($for == 'staff')
        {
            $query->whereIn('buyer_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('buyers','buyers.email','=','delegations.staff_email')
                ->join('buyer_companies', 'buyer_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('buyer')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('buyer_companies.buyer_id', \Auth::guard('buyer')->user()->id);
        }
        $query->distinct();
        $sellers =  $query->get([
            'buyer_companies.id as buyer_company_id',
            'seller_companies.id as seller_company_id',
            'seller_companies.seller_company_name',
            'seller_companies.seller_owner_email',
            'seller_companies.seller_phone_number',
            'seller_companies.seller_name',
            'seller_companies.seller_email',
            'seller_companies.languages',
            'seller_companies.currencies',
            'seller_companies.address',
            'seller_companies.country',
            'seller_companies.county',
            'seller_companies.county_l4',
            'seller_companies.last_order_at',
    
            'price_lists_extended.price_list',
            'price_lists_extended.department',
            'price_lists_extended.deleted_at',
            
            'product_list_requests.delivery_location_id as seller_delivery_location_id',
        ])
            ->toArray();
      
        $price_lists    =   [];
        foreach ($sellers as $seller_company)
        {
            if(isset($seller_company->seller_company_id))
            {
                if( $seller_company->deleted_at == null)
                {
                 
    
                    $seller_companies[ 'seller_price_lists'][$seller_company->buyer_company_id][$seller_company->seller_company_id][$seller_company->department]
                        = json_decode($seller_company->price_list,true);
                }
               
                
                $seller_companies[$seller_company->buyer_company_id][$seller_company->seller_company_id] = [
                    'id'                    =>  $seller_company->seller_company_id,
                    'company_name'          =>  $seller_company->seller_company_name,
                    'seller_owner_email'    =>  $seller_company->seller_owner_email,
                    'languages'             =>  json_decode($seller_company->languages,true)    ,
                    'currencies'            =>  json_decode($seller_company->currencies,true)    ,
                    'address'               =>  json_decode($seller_company->address,true)    ,
                    'country'               =>  $seller_company->country,
                    'county'                =>  $seller_company->county,
                    'county_l4'             =>  $seller_company->county_l4,
                    'seller_delivery_location_id'   =>  $seller_company->seller_delivery_location_id,
                    'last_order_at'         =>  $seller_company->last_order_at,
                    'seller_phone_number'   =>  $seller_company->seller_phone_number,
                    'seller_name'           =>  $seller_company->seller_name,
                    'seller_email'           =>  $seller_company->seller_email,
                  
                   
                   
                   
                
                ];
                
            }
            else
            {
                $seller_companies[ 'seller_price_lists'][$seller_company->buyer_company_id] = null;
                $seller_companies[$seller_company->buyer_company_id]   =   null;
            }
        }
  
        session()->put(  'seller_companies'  ,  $seller_companies  );
      
        return $seller_companies;
    }
    static function statistics($for)
    {
    
        $order_statistics = [];
        $query =  DB::table('buyer_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','buyer_companies.id')
            ->leftJoin('purchase_sales_statistics','purchase_sales_statistics.buyer_company_id','=','buyer_companies.id');
        if($for == 'staff')
        {
            $query->whereIn('buyer_companies.id',  DB::table('delegations')
            
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('buyers','buyers.email','=','delegations.staff_email')
                ->join('buyer_companies', 'buyer_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('buyer')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('buyer_companies.buyer_id', \Auth::guard('buyer')->user()->id);
        }
    
        $statistics =  $query->get([
           
            'buyer_companies.id as buyer_company_id',
           
            'purchase_sales_statistics.order_value',
            'purchase_sales_statistics.department',
            'purchase_sales_statistics.product_list'])
            ->toArray();
        ;
        foreach($statistics as $key => $statistic) {
    
            if ($statistic->product_list != '') {
                $order_statistics[ $statistic->buyer_company_id ][ $statistic->department ][ 'order_value' ][ $statistic->order_value ] = 1;
                $order_statistics[ $statistic->buyer_company_id ][ $statistic->department ][ 'statistics_product_list' ]
                    = json_decode($statistic->product_list, true);
            } else {
                $order_statistics[ $statistic->buyer_company_id ] = null;
            }
        }
       
        session()->put(  'statistics'  ,  $order_statistics  );
        return $order_statistics;
    }
    static function product_lists($for)
{
    $product_lists = [];
    $query =  DB::table('buyer_companies')
        ->leftJoin('delegations','delegations.delegator_company_id','=','buyer_companies.id')
        ->leftJoin('product_lists','product_lists.buyer_company_id','=','buyer_companies.id');
    if($for == 'staff')
    {
        $query->whereIn('buyer_companies.id',  DB::table('delegations')
            
            ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
            ->join('buyers','buyers.email','=','delegations.staff_email')
            ->join('buyer_companies', 'buyer_companies.id','=','delegations.delegator_company_id')
            ->where('delegations.staff_id', \Auth::guard('buyer')->user()->id)
            ->pluck('delegations.delegator_company_id')
            ->toArray());
    }
    elseif($for == 'owner')
    {
        $query->where('buyer_companies.buyer_id', \Auth::guard('buyer')->user()->id);
    }
    
    $product_list_departments =  $query->get([
        
        'buyer_companies.id as buyer_company_id',
        
        'product_lists.department',
        'product_lists.language',
        'product_lists.product_list',])
        ->toArray();
    ;
  
    foreach($product_list_departments as $key => $product_list) {
    
        $product_lists[ $product_list->buyer_company_id][$product_list->department][$product_list->language]
            =  $product_list->product_list;
        
    }
  
    session()->put(  'product_lists'  ,  $product_lists  );
    return $product_lists;
}
    static function price_lists_for_buyer($for)
    {
       
        $query =  DB::table('buyer_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','buyer_companies.id')
            ->leftJoin('price_lists','price_lists.buyer_company_id','=','buyer_companies.id')
            ->leftJoin('seller_companies','seller_companies.id','=','price_lists.seller_company_id')
            ->distinct();
        if($for == 'staff')
        {
            $query->whereIn('buyer_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('buyers','buyers.email','=','delegations.staff_email')
                ->join('buyer_companies', 'buyer_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('buyer')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('buyer_companies.buyer_id', \Auth::guard('buyer')->user()->id);
        }
        
        $price_list_departments =  $query->get([
            
            'buyer_companies.id as buyer_company_id',
            'price_lists.id as price_list_id',
            'price_lists.seller_id',
            'price_lists.seller_company_id',
            'price_lists.delivery_location_id',
            'price_lists.delivery_days',
            'price_lists.payment_frequency',
            'price_lists.department',
            'price_lists.activated_by_seller',
            'price_lists.activated_by_buyer',
            'price_lists.seller_disabled_currency',
            'price_lists.buyer_disabled_currency',
            'price_lists.seller_disabled_language',
            'price_lists.buyer_disabled_language',
            'price_lists.price_list',
            'price_lists.currency',
            'price_lists.language',
            'price_lists.country',
            'price_lists.county',
            'price_lists.county_l4',
            'seller_companies.seller_company_name',
           
            
           ])
            ->toArray();
        ;
        $price_lists    =   [];
        $payment_frequency  =   [];
        $delivery_days  =   [];
      
        foreach($price_list_departments as $key => $price_list) {
            if($price_list->buyer_company_id != null)
            {
                if($price_list->seller_company_id != null)
                {
                    $payment_frequency[$price_list->buyer_company_id][$price_list->seller_company_id][$price_list->department]
                        =  $price_list->payment_frequency;
                    $delivery_days[$price_list->buyer_company_id][$price_list->seller_company_id][$price_list->department]
                        = json_decode($price_list->delivery_days,true);
                }
               else
               {
                   $payment_frequency[$price_list->buyer_company_id]= null;
                    $delivery_days[$price_list->buyer_company_id]= null;
               }
               
                $price_list->price_list = json_decode($price_list->price_list,true);
                $price_list->delivery_days = json_decode($price_list->delivery_days,true);
    
    
                $price_lists['payment_frequency']   =   $payment_frequency;
                $price_lists['delivery_days']  =   $delivery_days;
              
              
                if(isset( $price_list->price_list))
                {
                    $price_lists[ $price_list->buyer_company_id][$price_list->department][ $price_list->seller_company_id]
                        =  $price_list;
                }
                else
                {
                    $price_lists[ $price_list->buyer_company_id] = null;
                }
               
            }
            
            
           
            
        }
      
        session()->put(  'price_lists'  ,  $price_lists  );
        return $price_lists;
    }
    static function companies_for_buyer($for)
    {
      
        $query =  DB::table('buyer_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','buyer_companies.id') ;
        if($for == 'staff')
        {
            $query->whereIn('buyer_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('buyers','buyers.email','=','delegations.staff_email')
                ->join('buyer_companies', 'buyer_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_email', \Auth::guard('buyer')->user()->email)
                ->where('delegations.undelegated_at', null)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('buyer_companies.buyer_id', \Auth::guard('buyer')->user()->id);
        }
        
        $companies =  $query->get([
            'buyer_companies.country',
            'buyer_companies.county',
            'buyer_companies.county_l4',
            
            'buyer_companies.buyer_company_name',
            'buyer_companies.id as buyer_company_id',
            'buyer_companies.VAT_number',
            
            'buyer_companies.buyer_name',
            'buyer_companies.buyer_email',
            'buyer_companies.buyer_phone_number',
            
            'buyer_companies.buyer_accountant_name',
            'buyer_companies.buyer_accountant_email',
            'buyer_companies.buyer_accountant_phone_number',
            
            'buyer_companies.buyer_owner_name',
            'buyer_companies.buyer_owner_email',
            'buyer_companies.buyer_owner_phone_number',
            
            'buyer_companies.currencies',
            'buyer_companies.languages',
            'buyer_companies.address',
            'buyer_companies.buyer_id',
    
        ])
            ->toArray();
        
      
        session()->put(  'buyer_companies'  ,  $companies  );
        return $companies;
    }
  
    static function seller_companies($for)
    {
        /*WHEN UPDATING ANY DATA FROM THE GROUP WE AER PULLING OWNER COMPANY AND MEMBER OF THE GROUP FROM SESSION*/
       $owner_companies         =   [];

       $staff                   =  session()->has('staff') ? session()->get('staff') :
           Company::seller_staff($for);

       $buyer_companies         =  session()->has('buyer_companies') ? session()->get('buyer_companies') :
           Company::buyer_companies_for_seller($for);

       $price_lists             =  session()->has('price_lists') ? session()->get('price_lists') :
           Company::price_lists($for);

       $product_lists_for_seller=  session()->has('product_lists') ? session()->get('product_lists') :
           Company::product_lists_for_seller($for);

       $cooperation_requests    =  session()->has('cooperation_requests') ? session()->get('cooperation_requests') :
           Company::cooperation_requests($for);

       $delivery_locations      =  session()->has('delivery_locations') ? session()->get('delivery_locations') :
           Company::delivery_locations($for);

       $price_lists_extended    =  session()->has('price_lists_extended') ? session()->get('price_lists_extended') :
           Company::price_lists_extended($for);

       $companies               =  session()->has('seller_companies') ? session()->get('seller_companies') :
           Company::companies_for_seller($for);

  
        foreach($companies as $key => $company)
        {
       
            $owner_companies[$company->seller_company_id] = (object)
            [
                'currencies'                 =>      json_decode( $company->currencies,true) ,
                'languages'                  =>      json_decode( $company->languages,true) ,
                'delivery_days'              =>      json_decode( $company->delivery_days,true) ,
                'address'                    =>      json_decode( $company->address,true) ,
                'payment_method'             =>      $company->payment_method,
                'seller_company_name'        =>      $company->seller_company_name,
                'id'                         =>      $company->seller_company_id,
                'VAT_number'                 =>      $company->VAT_number,
                'country'                    =>      $company->country,
                'county'                     =>      $company->county,
                'county_l4'                  =>      $company->county_l4,
                'seller_owner_name'          =>      $company->seller_owner_name,
                'seller_owner_email'         =>      $company->seller_owner_email,
                'seller_owner_phone_number'  =>      $company->seller_owner_phone_number,
    
                'seller_name'                =>      $company->seller_name,
                'seller_email'               =>      $company->seller_email,
                'seller_phone_number'        =>      $company->seller_phone_number,
    
                'seller_accountant_name'     =>      $company->seller_accountant_name,
                'seller_accountant_email'    =>      $company->seller_accountant_email,
          'seller_accountant_phone_number'   =>      $company->seller_accountant_phone_number,
    
                'seller_delivery_name'       =>      $company->seller_delivery_name,
                'seller_delivery_email'      =>      $company->seller_delivery_email,
              'seller_delivery_phone_number' =>      $company->seller_delivery_phone_number,
               
               
                
                'logged_in_staff'            =>      isset($staff[$company->seller_company_id]['logged_in_staff'])
                    ? $staff[$company->seller_company_id]['logged_in_staff'] : null,

                'staff_ids'            =>      isset($staff[$company->seller_company_id]['staff_ids'])
                    ? $staff[$company->seller_company_id]['staff_ids'] : [],

                'staff'                      =>      isset($staff[$company->seller_company_id]) ?
                    $staff[$company->seller_company_id] : null,

                'price_lists'                =>      isset($price_lists[ $company->seller_company_id])
                    ? $price_lists[ $company->seller_company_id] : null,

                'buyer_companies'            =>      isset($buyer_companies[$company->seller_company_id])
                    ? $buyer_companies[$company->seller_company_id] : null,

                'delivery_locations'         =>      isset($delivery_locations[$company->seller_company_id])
                    ? $delivery_locations[$company->seller_company_id] : null,

                'delivery_locations_search'  =>     isset($delivery_locations['for_search'])
                    ? $delivery_locations['for_search'] : null,

                'delivery_locations_index'  =>     isset($delivery_locations['index'])
                    ? $delivery_locations['index'] : null,

                'price_lists_extended'       =>     isset($price_lists_extended[$company->seller_company_id])
                    ? $price_lists_extended[$company->seller_company_id] : null,

                'product_lists'              =>     isset($product_lists_for_seller[$company->seller_company_id])
                    ? $product_lists_for_seller[$company->seller_company_id] : null,

                'cooperation_requests'              =>     isset($cooperation_requests[$company->seller_company_id])
                    ? $cooperation_requests[$company->seller_company_id] : null
                
            ];
        }
      
        return $owner_companies;
    }
    
    /* for seller company*/
    static function seller_staff($for)
    {
       
        
        $query =  DB::table('seller_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id')
            ->leftJoin('work_scopes','work_scopes.delegation_id','=','delegations.id')
            ->leftJoin('sellers','sellers.id','=','delegations.staff_id')
            ->where('delegations.delegator_role','seller_owner');
        
        
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_email', \Auth::guard('seller')->user()->email)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        
        $employees =  $query->get([
            'seller_companies.country',
            'seller_companies.county',
            'seller_companies.county_l4',
            'seller_companies.seller_company_name',
            'seller_companies.id as seller_company_id',
            'seller_companies.seller_email',
            'seller_companies.seller_phone_number',
            'seller_companies.seller_accountant_email',
            'seller_companies.seller_accountant_phone_number',
            'seller_companies.seller_delivery_email',
            'seller_companies.seller_delivery_phone_number',
            'seller_companies.seller_owner_email',
            'seller_companies.currencies',
            'seller_companies.languages',
            'seller_companies.delivery_days',
            'seller_companies.payment_method',
            
            'delegations.staff_id',
            'delegations.id as delegation_id',
            'delegations.delegator_company_name',
            'delegations.staff_name',
            'delegations.staff_role',
            'delegations.staff_position',
            'delegations.staff_email',
            'delegations.delegated_at',
            'delegations.accepted_at',
            'delegations.undelegated_at',
            
            'work_scopes.details as scope',
            'work_scopes.staff_phone_number',
            
            'sellers.duties as staff_duties',
        
        ])
            ->toArray();
        
       
        $staff=[];
        $logged_in_staff = null;
        
        foreach($employees as $key=>$employee)
        {
            /*SELLER STAFF*/
            
          
           
                
                if(explode('_',$employee->staff_role)[1] == 'accountant' && $employee->staff_position ==  'manager')    {
                    $phone_number = 'seller_accountant_phone_number';
                    $email        =   'seller_accountant_email';
                }
                elseif(explode('_',$employee->staff_role)[1] == 'seller'  && $employee->staff_position ==  'manager')   {
                    $phone_number = 'seller_phone_number';
                    $email        =   'seller_email';
                }
                elseif(explode('_',$employee->staff_role)[1] == 'delivery'  && $employee->staff_position ==  'manager')   {
                    $phone_number = 'seller_delivery_phone_number';
                    $email        =   'seller_delivery_email';
                }
                elseif( $employee->staff_position ==  'staff')   {
                    $phone_number = 'staff_phone_number';
                    $email        =   'staff_email';
                }
                if($for ==  'staff' && $employee->staff_email == \Auth::guard('seller')->user()->email)
                {
                    $logged_in_staff[$employee->seller_company_id] = [
                        'delegated_at'          =>      $employee->delegated_at,
                        'undelegated_at'        =>      $employee->undelegated_at,
                        'accepted_at'           =>      $employee->accepted_at,
                        'role'                  =>      $employee->staff_role,
                        'staff_position'        =>      $employee->staff_position,
                        'company_id'            =>      $employee->seller_company_id,
                        'staff_id'              =>      $employee->staff_id,
                        'staff_email'           =>      $employee->staff_email,
                        'scope'                 =>      json_decode($employee->scope,true),
                        'duties'                =>      json_decode($employee->staff_duties,true),
                    ];
                }
                
                $staff[$employee->seller_company_id][$employee->staff_role][ hash('adler32',$employee->staff_role.$employee->staff_position.$employee->staff_id) ]
                    = [
                    'delegated_at'          =>      $employee->delegated_at,
                    'undelegated_at'        =>      $employee->undelegated_at,
                    'accepted_at'           =>      $employee->accepted_at,
                    'scope'                 =>      json_decode($employee->scope,true),
                    'duties'                =>      json_decode($employee->staff_duties,true),
                    'staff_name'            =>      $employee->staff_name,
                    'role'                  =>      $employee->staff_role,
                    'staff_position'        =>      $employee->staff_position,
                    'staff_id'              =>      $employee->staff_id,
                    'delegation_id'         =>      $employee->delegation_id,
                    'delegator_company_name'=>      $employee->delegator_company_name,
                    'phone_number'          =>      $employee->$phone_number,
                    'email'                 =>      $employee->$email,
                
                ];
    
    
            $staff[$employee->seller_company_id] ['staff_ids'] [$employee->staff_role][$employee->staff_id] = 1  ;

            $staff[$employee->seller_company_id] ['logged_in_staff']  =
                !$logged_in_staff ? null:$logged_in_staff[$employee->seller_company_id];
        }
     //dd($staff);
        session()->put(  'staff'  ,  $staff  );
        return $staff;
        /*END OF SELLER STAFF*/
    }
    static function buyer_companies_for_seller($for)
    {
        $buyer_companies    =   [];
        $query =  DB::table('seller_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id')
            ->leftJoin('product_list_requests','product_list_requests.seller_company_id','=','seller_companies.id')
            ->leftJoin('buyer_companies','buyer_companies.id','=','product_list_requests.buyer_company_id');
        
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('seller')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        $query->distinct();
        $buyers =  $query->get([
            'seller_companies.id as seller_company_id',
            'buyer_companies.id as buyer_company_id',
            'buyer_companies.buyer_company_name',
            'buyer_companies.buyer_owner_name',
            'buyer_companies.buyer_owner_email',
            'buyer_companies.buyer_owner_phone_number',
            'buyer_companies.buyer_name',
            'buyer_companies.buyer_email',
            'buyer_companies.buyer_phone_number',
            'buyer_companies.buyer_accountant_name',
            'buyer_companies.buyer_accountant_email',
            'buyer_companies.buyer_accountant_phone_number',
            'buyer_companies.languages as buyer_company_languages',
            'buyer_companies.currencies as buyer_company_currencies',
            'buyer_companies.address as buyer_company_address',
            'buyer_companies.country as buyer_company_country',
            'buyer_companies.county as buyer_company_county',
            'buyer_companies.county_l4 as buyer_company_county_l4',
            
            'product_list_requests.delivery_location_id as buyer_delivery_location_id',
            'product_list_requests.department as department',
        ])
            ->toArray();
        $delivery_locations = [];
        foreach ($buyers as $buyer_company)
        {
            if(isset($buyer_company->buyer_company_id))
            {
                $delivery_locations[ $buyer_company->department] = $buyer_company->buyer_delivery_location_id;
                $buyer_companies[$buyer_company->seller_company_id][$buyer_company->buyer_company_id] = [
                    'id'                    =>  $buyer_company->buyer_company_id,
                    'company_name'          =>  $buyer_company->buyer_company_name,
                    'buyer_owner_email'     =>  $buyer_company->buyer_owner_email,
                    'buyer_owner_name'      =>  $buyer_company->buyer_owner_name,
                    'buyer_owner_phone_number'     =>  $buyer_company->buyer_owner_phone_number,
                    'buyer_email'           =>  $buyer_company->buyer_email,
                    'buyer_name'            =>  $buyer_company->buyer_name,
                    'buyer_phone_number'    =>  $buyer_company->buyer_phone_number,
                    'buyer_accountant_email'=>  $buyer_company->buyer_accountant_email,
                    'buyer_accountant_name' =>  $buyer_company->buyer_accountant_name,
                    'buyer_accountant_phone_number'     =>  $buyer_company->buyer_accountant_phone_number,
                    'languages'             =>  json_decode($buyer_company->buyer_company_languages,true)    ,
                    'currencies'            =>  json_decode($buyer_company->buyer_company_currencies,true)    ,
                    'address'               =>  json_decode($buyer_company->buyer_company_address,true)    ,
                    'country'               =>  $buyer_company->buyer_company_country,
                    'county'                =>  $buyer_company->buyer_company_county,
                    'county_l4'             =>  $buyer_company->buyer_company_county_l4,
                    'delivery_locations'    =>  $delivery_locations,
                
                ];
                
            }
            else
            {
                $buyer_companies[$buyer_company->seller_company_id]   =   null;
            }
        }
      
        session()->put(  'buyer_companies'  ,  $buyer_companies  );
        return $buyer_companies;
    }
    static function price_lists($for)
    {
        $price_list_departments=[];
        $query =  DB::table('seller_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id')
            ->leftJoin('price_lists','price_lists.seller_company_id','=','seller_companies.id');
        
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('seller')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        $query->distinct();
        $price_lists =  $query->get([
            'seller_companies.id as seller_company_id',
            'price_lists.id',
            'price_lists.department',
            'price_lists.language',
            'price_lists.currency',
            'price_lists.price_list',
            'price_lists.delivery_days',
            'price_lists.payment_frequency',
            'price_lists.activated_by_buyer',
            'price_lists.activated_by_seller',
            'price_lists.delivery_location_id',
            'price_lists.buyer_company_id',
            'price_lists.seller_id',
        
        ])
            ->toArray();
    
  
        foreach($price_lists as $price_list)
        {
            if(isset($price_list->price_list)){
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['id']
                    =   $price_list->id;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['seller_id']
                    =   $price_list->seller_id;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['price_list']
                    =   json_decode( $price_list->price_list,true);
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['language']
                    = $price_list->language;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['currency']
                    = $price_list->currency;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['delivery_days']
                    =  json_decode( $price_list->delivery_days,true);
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['payment_frequency']
                    =  $price_list->payment_frequency;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['activated_by_seller']
                    =  $price_list->activated_by_seller;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['activated_by_buyer']
                    =  $price_list->activated_by_buyer;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['delivery_location_id']
                    =  $price_list->delivery_location_id;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['seller_company_id']
                    =  $price_list->seller_company_id;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['buyer_company_id']
                    =  $price_list->buyer_company_id;
                $price_list_departments[ $price_list->seller_company_id][$price_list->department][$price_list->buyer_company_id]['department']
                    =  $price_list->department;
            }
            else
            {
                $price_list_departments[ $price_list->seller_company_id]/*[$price_list->buyer_company_id]*/ = null;
            }
        }
       
        session()->put(  'price_lists'  ,  $price_list_departments  );
        return $price_list_departments;
    }
    static function product_lists_for_seller($for)
    {
        $product_lists  =   [];
        $query =  DB::table('seller_companies')
            
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id')
            ->leftJoin('product_list_requests','product_list_requests.seller_company_id','=','seller_companies.id')
            ->leftJoin('product_lists','product_lists.buyer_company_id','=','product_list_requests.buyer_company_id');
        
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('seller')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        $query->distinct();
        $product_lists_for_seller =  $query->get([
            'seller_companies.id as seller_company_id',
    
            'product_lists.department as department',
            'product_lists.buyer_company_id as buyer_company_id',
            'product_lists.language as language',
            'product_lists.product_list as product_list',


        ])
            ->toArray();
        
        
        foreach($product_lists_for_seller as $product_list)
        {
            if(isset($product_list->product_list)){
                $product_lists[ $product_list->seller_company_id]
                [$product_list->buyer_company_id]
                [$product_list->department][ $product_list->language]
            
                    =   json_decode( $product_list->product_list,true) ;
        
            }
            else
            {
                $product_lists[ $product_list->seller_company_id][$product_list->buyer_company_id] = null;
            }
        }
    
        session()->put(  'product_lists'  ,  $product_lists  );
        return $product_lists;
    }
    static function cooperation_requests($for)
    {
        $cooperation_requests  =   [];
        
        $query =  DB::table('seller_companies')
            
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id')
            ->leftJoin('product_list_requests','product_list_requests.seller_company_id','=','seller_companies.id');
           
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('seller')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        $query->distinct();
        $cooperation_requests_for_seller =  $query->get([
            'product_list_requests.seller_company_id',
            'product_list_requests.buyer_company_id',
            'product_list_requests.department',
            'product_list_requests.guard',
            'product_list_requests.requested',
            'product_list_requests.responded',
        ])
            ->toArray();
        
        
        foreach($cooperation_requests_for_seller as $cooperation_request)
        {
            $cooperation_requests
            [$cooperation_request->seller_company_id]
            [$cooperation_request->buyer_company_id]
            [$cooperation_request->department]
            [$cooperation_request->guard]
            [$cooperation_request->requested]
            [$cooperation_request->responded]=1;
           
        }
       // dd($cooperation_requests);
        session()->put(  'cooperation_requests'  ,  $cooperation_requests  );
        return $cooperation_requests;
    }
    static function delivery_locations($for)
    {
       
        $query =  DB::table('seller_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id')
            ->leftJoin('delivery_locations','delivery_locations.seller_company_id','=','seller_companies.id');
        
        
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('seller')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        $query->distinct();
        $delivery_locations =  $query->get([
            
            'seller_companies.id as seller_company_id',
            
            'delivery_locations.id as delivery_location_id',
            'delivery_locations.seller_id',
            'delivery_locations.delivery_days as delivery_days',
            'delivery_locations.country as country',
            'delivery_locations.county as county',
            'delivery_locations.county_l4 as county_l4',
            'delivery_locations.department as department',
            'delivery_locations.seller_id as seller_id'])
            ->toArray();
     
        $delivery_locations_for_search = [];
        $paths = [];
        $delivery_location_delivery_days=[];
        foreach ($delivery_locations as $delivery_location) {
            if(isset($delivery_location->delivery_location_id))
            {
                $delivery_locations_for_search[$delivery_location->seller_company_id]
                [$delivery_location->seller_id]
                [$delivery_location->department]
                [$delivery_location->country]
                [$delivery_location->county]
                [$delivery_location->county_l4]
                =   1;
                
                $path   =   $delivery_location->country;
                
                if($delivery_location->county != null)
                {
                    $path   =   $delivery_location->county;
                }
                if($delivery_location->county_l4 != null)
                {
                    $path   =    $delivery_location->county_l4;
                }
                
                $paths[] = $path;
                
                $delivery_location_delivery_days
                [$delivery_location->seller_company_id]
                [$delivery_location->department]
                [$delivery_location->delivery_location_id] =
                    [
                        'delivery_days'    =>       $delivery_location->delivery_days,
                        'path'             =>       $path,
                        'base_location'    =>       $path
                    ] ;
            }
            else
            {
                $delivery_location_delivery_days[$delivery_location->seller_company_id] = null;
            }
        }
        $dl_full = LocationNameOrId::get_paths($paths, $delivery_location_delivery_days);
        $seller_delivery_locations                  =  $dl_full['dl_full'];
        $seller_delivery_locations['for_search']    =  $delivery_locations_for_search;
        $seller_delivery_locations['index']         =   $dl_full['dl_index'];
      
        session()->put(  'delivery_locations'  ,  $seller_delivery_locations  );
       
        return $seller_delivery_locations;
    }
    static function price_lists_extended($for)
    {
        $price_lists = [];
        $query =  DB::table('seller_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id')
            ->leftJoin('price_lists_extended','price_lists_extended.seller_company_id','=','seller_companies.id')
            ->leftJoin('price_list_translations','price_list_translations.seller_company_id','=','seller_companies.id')
            ->leftJoin('currency_conversion_rates','currency_conversion_rates.seller_company_id','=','seller_companies.id')
           ->where('price_lists_extended.deleted_at',null)
            ->where('price_list_translations.deleted_at',null);
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_id', \Auth::guard('seller')->user()->id)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        $query->distinct();
        $price_lists_extended =  $query->get([
            
            'seller_companies.id as seller_company_id',
            
            'price_lists_extended.department as price_list_extended_department',
            'price_lists_extended.price_list as price_list_extended',
            'price_lists_extended.deleted_at as price_list_extended_deleted_at',
            
            'price_list_translations.department as price_list_translations_department',
            'price_list_translations.translations as price_list_translations',
            'price_list_translations.deleted_at as price_list_translations_deleted_at',
            
            'currency_conversion_rates.rates as conversion_rates',
        ])
            ;
    
     // dd( $price_lists_extended->groupBy('price_list_extended_deleted_at')->toArray()['']);
       
        
        foreach($price_lists_extended as $price_list_extended)
        {
           
            if(isset($price_list_extended->price_list_extended) && $price_list_extended->price_list_extended_deleted_at == null)
            {
               
                $price_lists[$price_list_extended->seller_company_id] ['price_lists_extended'][$price_list_extended->price_list_extended_department]=
                    json_decode($price_list_extended->price_list_extended,true);
                
            }
            else
            {
                $price_lists[$price_list_extended->seller_company_id]['price_lists_extended'] = null;
            }
            if(isset($price_list_extended->price_list_translations)  && $price_list_extended->price_list_translations_deleted_at == null)
            {
                $price_lists[$price_list_extended->seller_company_id]['price_list_translations'] [$price_list_extended->price_list_translations_department]=
                    json_decode($price_list_extended->price_list_translations,true);
                
            }
            else
            {
                $price_lists[$price_list_extended->seller_company_id]['price_list_translations'] = null;
            }
            
            if(isset($price_list_extended->conversion_rates))
            {
                $price_lists[$price_list_extended->seller_company_id]['conversion_rates'] =
                    json_decode($price_list_extended->conversion_rates,true);
                
            }
            else
            {
                $price_lists[$price_list_extended->seller_company_id]['conversion_rates'] = null;
            }
            
        }
    
        session()->put(  'price_lists_extended'  ,  $price_lists  );
        return $price_lists;
    }
    static function companies_for_seller($for)
    {
     
        $query =  DB::table('seller_companies')
            ->leftJoin('delegations','delegations.delegator_company_id','=','seller_companies.id') ;
        if($for == 'staff')
        {
            $query->whereIn('seller_companies.id',  DB::table('delegations')
                
                ->leftJoin('work_scopes', 'delegations.id', '=', 'work_scopes.delegation_id')
                ->join('sellers','sellers.email','=','delegations.staff_email')
                ->join('seller_companies', 'seller_companies.id','=','delegations.delegator_company_id')
                ->where('delegations.staff_email', \Auth::guard('seller')->user()->email)
                ->where('delegations.undelegated_at', null)
                ->pluck('delegations.delegator_company_id')
                ->toArray());
        }
        elseif($for == 'owner')
        {
            $query->where('seller_companies.seller_id', \Auth::guard('seller')->user()->id);
        }
        
        $companies =  $query->get([
            'seller_companies.country',
            'seller_companies.county',
            'seller_companies.county_l4',
            
            'seller_companies.seller_company_name',
            'seller_companies.id as seller_company_id',
            'seller_companies.VAT_number',
            
            'seller_companies.seller_name',
            'seller_companies.seller_email',
            'seller_companies.seller_phone_number',
            
            'seller_companies.seller_accountant_name',
            'seller_companies.seller_accountant_email',
            'seller_companies.seller_accountant_phone_number',
            
            'seller_companies.seller_delivery_name',
            'seller_companies.seller_delivery_email',
            'seller_companies.seller_delivery_phone_number',
            
            'seller_companies.seller_owner_name',
            'seller_companies.seller_owner_email',
            'seller_companies.seller_owner_phone_number',
            
            'seller_companies.currencies',
            'seller_companies.languages',
            'seller_companies.delivery_days',
            'seller_companies.payment_method',
            'seller_companies.address',
            
           
        ])
            ->toArray();
      
        session()->put(  'seller_companies'  ,  $companies  );
        return $companies;
    }
    
}
