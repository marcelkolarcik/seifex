<?php

namespace App\Http\Controllers\Seller;

use App\Events\BuyerNotificationEvent;
use App\Events\SellerNotificationEvent;
use App\Jobs\DeliveryDaysEmailJob;

use App\Services\Departments;
use App\Services\LocationNameOrId;
use App\WorkScope;
use Illuminate\Http\Request;
use App\DefaultDepartment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Http\Controllers\Controller;
use App\Services\Role;
use App\Repository\CountryRepository;
use App\Services\DeliveryDays;
use Mavinoo\LaravelBatch\Batch;
use App\Services\Company;


class DeliveryController extends Controller
{


    public $location;
    public $role;
    public $deliveryDays;
    public $batch;
    public $company;
    public $departments;

    public function __construct(Company $company,
                                Batch $batch,
                                LocationNameOrId $location,
                                Role $role,
                                CountryRepository $countryRepository,
                                DeliveryDays $deliveryDays,
                                Departments $departments)
    {
    
        $this->role = $role;
        $this->company = $company;
        $this->deliveryDays = $deliveryDays;
        $this->location = $location;
        $this->departments = $departments;
        $this->batch  = $batch;
        $this->countryRepository = $countryRepository;
        $this->middleware('seller.auth:seller');
    }

    public function companies()
   {
       return $this->company->for($this->role->get_owner_or_staff());
   }
    public function locations(Request $request)
    {
        ////UPDATE COMPANY IN SESSION
       
       if(session()->has('location_expanded'))
       {
           session()->pull('location_expanded');
           $this->company->update(['delivery_locations']);
       }
       
        
        
        $seller_company_id  =   session()->get('company_id');
        $company            =   $this->companies()[$seller_company_id];
        $delivery_days      =   $company->delivery_days;
        $info               =   (object)    ['days' =>   $this->role->days()];
       
       
        $delivery_locations =   $company->delivery_locations ;
        $departments        =   $this->departments->for_sc($seller_company_id);
        $buyers_in_location =   [];
        
       if($company->price_lists != null)
       {
           foreach($company->price_lists as $pl_departments =>  $bc_ids)
           {
               foreach($bc_ids as $bc_id =>  $price_list)
               {
                   $buyers_in_location[$price_list['delivery_location_id']][]    =    $price_list['delivery_location_id'];
                   
               }
        
           }
       }
        
   
        $locations_active   =   'active';
        $country_levels     =   LocationNameOrId::current_countries_for_select();
        $county_levels      =   [];
        $county_levels_4    =   [];
        
        return view('seller.company.delivery.form',
            compact('buyers_in_location',
                'seller_company_id',
                'locations_active',
                'departments',
                'delivery_locations',
                'company',
                'country_levels',
                'county_levels',
                'county_levels_4',
                'info',
                'delivery_days'));
    }
    public function show ($delivery_location_id,$department)
    {
       
        $company = $this->companies()[session()->get('company_id')];
        $info               =   (object)    ['days' =>   $this->role->days()];
      
        $levels =   [
            2 =>  'country',
            3 =>  'county',
            4 =>  'county_l4'
        ];
        
        $delivery_location  =   $company->delivery_locations[$department][$delivery_location_id];
       
        $location_id = $delivery_location['location_id'];
        foreach($delivery_location['delivery_days'] as $day)
        {
            $delivery_days[]    =   $info->days[$day];
        }
        
        $level = sizeof(explode('.', $location_id));
        $level_name = $levels[$level];
        $staff_in_location  =   [];
        $buyers_in_location  =   [];
        $active_sellers_in_location  =   [];
        $revenue_buyers =   [];
        $revenue=[];
        
        if($company->price_lists != null)
        {
          //  dd($company->price_lists);
            foreach($company->price_lists as $pl_departments =>  $bc_ids)
            {
                foreach($bc_ids as $bc_id =>  $price_list)
             
                   if($delivery_location_id == $price_list['delivery_location_id'])
                   {
                       
                        $revenue_buyers[]   =   $price_list['buyer_company_id'];
                        $buyers_in_location[$price_list['buyer_company_id']]    =    $company->buyer_companies[$price_list['buyer_company_id']];
                        
                        
                      
                      
                       
                       if(/*\Auth::guard('seller')->user()->role == 'seller_owner' &&*/
                          // $price_list['seller_id'] == \Auth::guard('seller')->user()->id

                         $price_list['seller_id'] == $this->role->get_owner_id()
                       
                       )
                       {
                         
                           $active_sellers_in_location[$price_list['seller_id']]['department']
                               =   $price_list['department'];
    
                           $active_sellers_in_location[$price_list['seller_id']] ['details']['role']
                               =   'seller_owner';
    
                           $active_sellers_in_location[$price_list['seller_id']]['details'] ['staff_name']
                               =  $company->seller_owner_name;
    
                          
                       }
                       
                        foreach( $company->staff as $role    =>  $hash_details )
                        {
                            if($role == 'seller_seller' || $role == 'seller_delivery')
                            {
                              
                                foreach ($hash_details as $hash   =>  $staff_details) {
                
                                    if($price_list['seller_id'] == $staff_details['staff_id'])
                                        $active_sellers_in_location[$price_list['seller_id']]['department']
                                            =   $price_list['department'];
                                }
                            }
                        }
    
                   }
                }
           
        }
       
        $company_staff  =   $company->staff;
        
        foreach($company_staff as $role =>  $hash_data)
        {
            if($role == 'seller_seller' || $role == 'seller_delivery')
            {
                foreach($hash_data as $staff)
                {
                    !isset($staff['scope']['base_locations'][$level_name][$location_id]) ?
                        :
                        $staff_in_location[$role][] =   $staff;
                    
                    !isset($active_sellers_in_location[$staff['staff_id']]) ?
                        :
                        $active_sellers_in_location[$staff['staff_id']]['details'] = $staff;
                    
                    ;
                }
            }
        }
      
        if($revenue_buyers)
        {
           
            $orders = DB::table('orders')
                ->whereIn('buyer_company_id',$revenue_buyers)
                ->where('department',$department)
                ->get(['total_order_cost','currency','buyer_company_id','seller_id']);
           if($orders->count())
           {
              
               $grouped_currencies   = $orders->groupBy('currency');
    
               foreach($grouped_currencies as $currency =>   $currency_orders)
               {
                   $revenue[$currency]   =    $currency_orders->sum('total_order_cost');
               }
    
               $orders_by_buyer = $orders->groupBy(['buyer_company_id','currency']);
               foreach($orders_by_buyer as $bc_id =>   $currency_orders)
               {
                   foreach($currency_orders as  $currency => $bc_orders)
                       $buyers_in_location[$bc_id]['revenue'][$currency] = $bc_orders->sum('total_order_cost');
               }
               uasort($buyers_in_location, function($a, $b) {
                   return $b['revenue'] <=> $a['revenue'];
               });
    
              
               $orders_by_seller = $orders->groupBy(['seller_id','currency']);
               foreach($orders_by_seller as $s_id =>   $currency_orders)
               {
                   foreach($currency_orders as  $currency => $s_orders)
                       $active_sellers_in_location[$s_id]['revenue'][$currency] = $s_orders->sum('total_order_cost');
               }
               uasort($active_sellers_in_location, function($a, $b) {
                   return $b['revenue'] <=> $a['revenue'];
               });
           }
           
        }
     //  dd($active_sellers_in_location);
  /*TO DO CHECK WITH MULTIPLE CURRENCIES*/
        
        return view('seller.company.delivery.location', compact(
            'department',
            'delivery_location',
            'buyers_in_location',
            'active_sellers_in_location',
            'staff_in_location',
            'delivery_days',
            'revenue'));
    }
    public function update_location_delivery_days(Request $request)
    {
        if($request->ajax())
        {
            /* IF WE ARE UPDATING OUR LOCATION*/
            if(
            DB::table('delivery_locations')
                ->where('seller_company_id',session()->get('company_id'))
                ->where('id',$request->delivery_location_id)
                ->update([
                    'delivery_days' =>  json_encode($request->updated_delivery_days)
                ]))
            {
                $this->company->update('delivery_locations');
                return ['status'=>'updated','text'=>__('Delivery days updated !')];
            }
        
            
    
            return ['status'=>'error','text'=>__('Delivery days not updated !')];
        }
        
    }
    public function update_buyer_delivery_days(Request $request)
    {
    
        /* IF WE ARE UPDATING OUR LOCATION*/
      
            if(!DB::table('price_lists')
              
                ->where('seller_company_id',$request->seller_company_id)
                ->where('buyer_company_id',$request->buyer_company_id)
                ->where('department',$request->department)
                ->update([
                    'delivery_days'     =>  json_encode($request->updated_delivery_days),
                    'updated_at'        =>   date('Y-m-d H:i:s')
                ]))
            {
                return ['status'=>'not updated','text'=>__('Delivery days not updated !')];
            }
            
            session(['delivery_days_for_buyers_location' => $request->updated_delivery_days]);
            
            $sc_name  = $this->companies()[$request->seller_company_id]->seller_company_name;
            $bc_name  = $this->companies()[$request->seller_company_id]->buyer_companies[ $request->buyer_company_id]['company_name'];
            $bo_email = $this->companies()[$request->seller_company_id]->buyer_companies[ $request->buyer_company_id]['buyer_owner_email'];
           
            $details=[
                'n_link'             =>    '/department/'.$request->department.'/'.$request->buyer_company_id,
                'action'             =>   'payment_frequency',
                'buyer_owner_email'  =>  $bo_email,
                'buyer_company_name' =>  $bc_name,
                'buyer_company_id'   =>  $request->buyer_company_id,
                'seller_company_id'  =>  $request->seller_company_id,
                'subject'            =>  __('seller_company delivery days were changed for you.',
                    [
                        'seller_company'     =>  $sc_name,
                    ])
            ];
            /////    EMAIL
            DeliveryDaysEmailJob::dispatch($details);
    
            /////    PUSHER
            BuyerNotificationEvent::dispatch($details);
            
            ////UPDATE COMPANY IN SESSION
            $this->company->update('delivery_locations');
            
            return ['status'=>'updated','text'=>__('Delivery days updated !')];
            
        
    }
    public function expand_delivery_locations(Request $request)
    {
        
        if($request->delivery_days == null)
        {
            $request->request->add([
                'delivery_days'=>$this->companies()[$request->seller_company_id]->delivery_days]);
       
        }
        
        $delivery_locations = $this->companies()[$request->seller_company_id]->delivery_locations_search;
	    //// IF THE ENTRY IS A DUPLICATE , THEN RETURN WITH ERROR MESSAGE
     
	    if(
	    isset(
                $delivery_locations
                [$request->seller_company_id]
                [$this->role->get_owner_id()]
                [str_replace(' ','_',$request->department)]
                [$request->country]
                [$request->county]
                [$request->county_l4] )
       
	    )
	    {
      
		    return redirect()->back()->with([
			
			    'delivery_locations'        =>  $delivery_locations,
			    'duplicate'                 =>  true,
			    'dep'                       =>  $request->department,
                'country'                   =>  LocationNameOrId::get_name($request->country) ,
                'county'                    =>  LocationNameOrId::get_name($request->county) ,
                'county_l4'                 =>  LocationNameOrId::get_name($request->county_l4) ,
			   ]);
	    }
	    
	    //// IF THE ENTRY IS A SUB_SET OF ALREADY EXISTING DELIVERY LOCATION ,
	    ///
	    ///  FOR EXAMPLE:
	    ///  IF YOU ARE TRYING TO ADD DELIVERY LOCATION OF A COUNTY_L4
	    ///  BUT YOU ALREADY HAVE DELIVERY LOCATION FOR WHOLE COUNTRY
	    ///  => RETURN ERROR MESSAGE, EXPLAINING , THAT IF SELLER WANTS TO
	    ///   CHANGE LOCATION TO ONLY SUB-LOCATION, HE NEEDS TO DELETE HIGHIER LEVEL LOCATION FIRST
	    ///
	    
	   // dd($request->request);
	    if($request->county_l4 != '')
	    {
		    if( isset(
                $delivery_locations
                [$request->seller_company_id]
                [$this->role->get_owner_id()]
                [str_replace(' ','_',$request->department)]
                [$request->country]
                ['']
                [''] )
		    )
		    {
			    return redirect()->back()->with([
			    	
				    'delivery_locations'        =>  $delivery_locations,
				    'child_location'            =>  true,
				    'main_location'             =>  LocationNameOrId::get_name($request->country) ,
				    'dep'                       =>  $request->department,
			    ]);
		    }
			elseif( isset(
                $delivery_locations
                [$request->seller_company_id]
                [$this->role->get_owner_id()]
                [str_replace(' ','_',$request->department)]
                [$request->country]
                [$request->county]
                [''] )
			    )
			{
				    return redirect()->back()->with([
					
					    'delivery_locations'        =>  $delivery_locations,
					    'child_location'            =>  true,
					    'main_location'             =>  LocationNameOrId::get_name($request->county) ,
					    'dep'                       =>  $request->department,
					    
				    ]);
			}
	     }
	    if($request->county != '' && $request->county_l4 == '')
	    {
		    if( isset(
                $delivery_locations
                [$request->seller_company_id]
                [$this->role->get_owner_id()]
                [str_replace(' ','_',$request->department)]
                [$request->country]
                ['']
                [''] )
		    )
		    {
			    return redirect()->back()->with([
				
				    'delivery_locations'        =>  $delivery_locations,
				    'child_location'     =>  true,
				    'main_location'             =>  LocationNameOrId::get_name($request->country) ,
				    'dep'                       =>  $request->department,
			
			    ]);
		    }
	    }
	    if($request->county == '')
	    {
		    if( isset(
                $delivery_locations
                [$request->seller_company_id]
                [$this->role->get_owner_id()]
                [str_replace(' ','_',$request->department)]
                [$request->country]
                ['']
                [''] )
		    )
		    {
			    return redirect()->back()->with([
				
				    'delivery_locations'        =>  $delivery_locations,
				    'child_location'            =>  true,
				    'main_location'             =>  LocationNameOrId::get_name($request->country) ,
				    'dep'                       =>  $request->department,
			
			    ]);
		    }
	    }
	
	   
	    ///   NEW ENTRY
	    ///
	  
	   
	    if(  ! isset(
            $delivery_locations
            [$request->seller_company_id]
            [$this->role->get_owner_id()]
            [str_replace(' ','_',$request->department)]
            [$request->country]
            [$request->county]
            [$request->county_l4] )
	    )
	    {
	     
		    //// NEW ENTRY
		    ///  IF THE ENTRY IS GOING TO COVER  ALREADY EXISTING DELIVERY LOCATION ,
		    /// ON LOWER LEVEL
		    ///  FOR EXAMPLE:
		    ///  IF YOU ARE TRYING TO ADD DELIVERY LOCATION FOR WHOLE COUNTRY
		    ///  BUT YOU ALREADY HAVE DELIVERY LOCATION FOR COUNTIES
		    ///  => RETURN SUCCESS MESSAGE, EXPLAINING , THAT
		    ///   LOCATION INCLUDES ALL SUB-LOCATIONS
		    ///
		    ///
		   if($request->country && $request->county == '')
		    {
			    $replaced_locations = $this->delete_replace_locations($request, 'country', $request->department);
			  
                $this->notification_for_sellers($request->seller_company_id);
               
			    return redirect()->back()->with([
				    'dep'               => $request->department,
				    'delivery_locations'=> $delivery_locations,
				    'expanded'          => 'true',
				    'location_expanded' => true,
				    'replaced_locations'=> 	$replaced_locations
			    ]);
		    }
		
		    elseif($request->county && $request->county_l4 == '')
		    {
		    
			    $replaced_locations = $this->delete_replace_locations($request, 'county', $request->department);
			    
                $this->notification_for_sellers($request->seller_company_id);
                
			    return redirect()->back()->with([
				    'dep'               => $request->department,
				    'delivery_locations'=> $delivery_locations,
				    'expanded'          => 'true',
				    'location_expanded' => true,
				    'replaced_locations'=> 	$replaced_locations
			    ]);
		    }
		   //// NEW ENTRY NO SUBLEVELS
		  
		    elseif($request->county_l4 != '')
		    {
       
			    DB::table('delivery_locations')
				    ->insert([
					    'seller_id'         =>   \Auth::guard('seller')->user()->id,
                        'seller_company_id' =>   $request->seller_company_id,
					    'country'           =>   $request->country,
					    'county'            =>   $request->county,
					    'county_l4'         =>   $request->county_l4,
					    'department'        =>   str_replace(' ','_',$request->department),
                        'delivery_days'     =>   json_encode($request->delivery_days),
					    'created_at'        =>   date('Y-m-d H:i:s'),
					    'updated_at'        =>   date('Y-m-d H:i:s')
				    ]);
			    
                $this->notification_for_sellers($request->seller_company_id);
            
                return redirect()->action(
                    'Seller\DeliveryController@locations'
                    
                )->with([
                    'delivery_locations'=> $delivery_locations,
                    'expanded'          => 'true',
                    'location_expanded' => true
                ]);
                
                
			  
		    }
		   
        
        
        }
	    
    }
	public function delete_delivery_location(Request $request)
	{
	
		
		$deleted_location =  DB::table('delivery_locations')
            ->where('id','=',$request->delivery_location_id)
            ->where('seller_company_id',session()->get('company_id'))
            ->get(['country','county','county_l4','seller_company_id'])
            ->first();
     
		$deleted_location_name  =   LocationNameOrId::get_name($deleted_location->country);
        $deleted_location_id  =  $deleted_location->country;
        
        if($deleted_location->county !== null) {
            $deleted_location_name  = LocationNameOrId::get_name($deleted_location->county) ;
            $deleted_location_id  =  $deleted_location->county;
        }
		if($deleted_location->county_l4 !== null) {
		    $deleted_location_name  = LocationNameOrId::get_name($deleted_location->county_l4) ;
            $deleted_location_id  =  $deleted_location->county_l4;
        }
      
        
        $scope_of_work  =   DB::table('delegations')
            ->leftJoin('work_scopes','delegations.id','=','work_scopes.delegation_id')
            ->where('delegations.delegator_company_id',session()->get('company_id'))
            ->whereIn('delegations.staff_role',['seller_seller','seller_delivery'])
            ->where('undelegated_at',null)
            ->where('accepted_at','!=',null)
            ->get(['work_scopes.details','work_scopes.id'])
            ->toArray();
      
        /*GETTING RID OF EMPTY SCOPES*/
        unset($scope_of_work [''] );
        
       
     
     $e=0;
        foreach($scope_of_work as   $details)
        {
            
            $id         =    $details->id;
            $scope_details    =    json_decode($details->details,true);
            
            $updated_scope_of_work[$e ]  = ['id'   =>   $id, 'details' =>   $scope_details]   ;
             
               
               $base_locations =   json_decode($details->details,true)['base_locations'];
          
                foreach($base_locations as $level => $locations)
                {
                    if(isset($base_locations[$level][$deleted_location_id]))
                    {
                      
                        unset($base_locations[$level][$deleted_location_id]);
                      
                    }
                }
                
               $last_locations =   json_decode($details->details,true)['last_locations'];
               foreach($last_locations as $level => $locations)
               {
                if(isset($last_locations[$level][$deleted_location_id])) unset($last_locations[$level][$deleted_location_id]);
               }
    
            $scope_details['last_locations'] =   $last_locations;
            $scope_details['base_locations'] =   $base_locations;
            
            $updated_scope_of_work[$e ]  = ['id'   =>   $details->id, 'details' => json_encode($scope_details)  ]   ;
          $e++;
           
        }
    
        $this->batch->update(new WorkScope, $updated_scope_of_work);
   
   
        
			if(DB::table('delivery_locations')
                ->where('seller_company_id','=',$deleted_location->seller_company_id)
				->where('id','=',$request->delivery_location_id)
				->where('department','=',$request->department)
				->delete())
			
            {
                $price_lists    =     DB::table('price_lists')
                    ->where('seller_company_id','=',$deleted_location->seller_company_id)
                    ->where('department','=',str_replace(' ','_',$request->department));
                
                $product_list_requests  =      DB::table('product_list_requests')
                    ->where('seller_company_id','=',$deleted_location->seller_company_id)
                    ->where('department','=',str_replace(' ','_',$request->department));
                
                if($deleted_location->county_l4 != null)
                {
                    ///// DELETE FROM price_lists
                    $price_lists
                        ->where('country', $deleted_location->country)
                        ->where('county', $deleted_location->county)
                        ->where('county_l4', $deleted_location->county_l4);
                    
                    ///// DELETE FROM stockListRequests
    
                    $product_list_requests
                        ->where('country', $deleted_location->country)
                        ->where('county', $deleted_location->county)
                        ->where('county_l4', $deleted_location->county_l4);
                        
                }
                elseif($deleted_location->county != null && $deleted_location->county_l4 == null)
                {
                    ///// DELETE FROM price_lists
                    $price_lists
                        ->where('country', $deleted_location->country)
                        ->where('county', $deleted_location->county);
                    
                    ///// DELETE FROM stockListRequests
    
                    $product_list_requests
                        ->where('country', $deleted_location->country)
                        ->where('county', $deleted_location->county);
                }
                elseif($deleted_location->country != null && $deleted_location->county == null)
                {
                    ///// DELETE FROM price_lists
                    $price_lists
                        ->where('country', $deleted_location->country);
    
    
                    ///// DELETE FROM stockListRequests
    
                    $product_list_requests
                        ->where('country', $deleted_location->country) ;
                }
    
               
                $price_lists->delete();
                $product_list_requests->delete();
                
               
                ////UPDATE COMPANY IN SESSION
                $this->company->update(['delivery_locations','staff','price_lists','product_lists','seller_companies']);
    
    
                //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
                $details=[
                    'n_link'                    =>   '/department/'. $request->department,
                    'action'                    =>   'delivery_location_deleted',
                    'seller_company_id'         =>  $deleted_location->seller_company_id,
                    'seller_company_name'       =>  $this->companies()[$deleted_location->seller_company_id]->seller_company_name,
                    'department'                =>  $request->department,
                    'deleted_location'          =>  str_replace('-',' ',str_replace('--',' | ',$deleted_location_name)),
                    'subject'                   =>  __('seller_company ceased delivery to location in department.',
                        [
                            'seller_company'    =>  $this->companies()[$deleted_location->seller_company_id]->seller_company_name,
                            'location'          =>  str_replace('-',' ',str_replace('--',' | ',$deleted_location_name)),
                            'department'        =>  $request->department
                        ]),
                ];
                /////    PUSHER  //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
                BuyerNotificationEvent::dispatch($details);
    
                $subject   =   __('seller_name has deleted delivery location delivery_location.',
                    [
                        'seller_name'            =>   \Auth::guard('seller')->user()->name,
                        'delivery_location'    =>    str_replace('-',' ',str_replace('--',' | ',$deleted_location_name))
                    ]);
                //// NOTIFY LOGGED IN SELLERS FROM THE COMPANY BY PUSHER AND RELOAD SESSION ON THEIR SIDE
                $this->notification_for_sellers($deleted_location->seller_company_id,$subject);
              
                
                return ['status'=>'deleted',
                        'location'=>str_replace('-',' ',str_replace('--',' | ',$deleted_location_name)),
                        'text'=>__(' was deleted !')];
              
            }
            
            else
            {
                abort('401');
            }
			
		
	}
	public function delete_delivery_department(Request $request)
	{
        
    
	   if( DB::table('delivery_locations')
           ->where('seller_company_id','=',$request->seller_company_id)
           ->where('department','=',str_replace(' ','_',$request->department))
           ->delete())
       {
           DB::table('price_lists')
               ->where('seller_company_id','=',$request->seller_company_id)
               ->where('department','=',str_replace(' ','_',$request->department))
               ->delete();
    
           DB::table('product_list_requests')
               ->where('seller_company_id','=',$request->seller_company_id)
               ->where('department','=',str_replace(' ','_',$request->department))
               ->delete();
         
           ////UPDATE COMPANY IN SESSION
           $this->company->update(['delivery_locations','price_lists','product_lists','seller_companies']);
           
           
           //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
           $details=[
               'n_link'                         =>   '/department/'. $request->department,
               'action'                         =>   'delivery_department_deleted',
               'seller_company_id'              =>  $request->seller_company_id,
               'seller_company_name'            =>  $this->companies()[$request->seller_company_id]->seller_company_name,
               'deleted_department'             =>  $request->department,
               'subject'            =>  __('seller_company ceased delivery for department.',
                   [
                       'seller_company'    =>  $this->companies()[$request->seller_company_id]->seller_company_name,
                       'department'        =>  $request->department
                   ]),
           ];
           /////    PUSHER
           BuyerNotificationEvent::dispatch($details);
    
           $subject   =   __('seller_name has deleted delivery department delivery_department.',
               [
                   'seller_name'            =>   \Auth::guard('seller')->user()->name,
                   'delivery_department'    =>    str_replace(' ','_',$request->department)
               ]);
           
            $this->notification_for_sellers($request->seller_company_id,$subject);
            
           return ['status'=>'deleted','department'=>str_replace(' ','_',$request->department),'text'=>__(' was deleted !')];
       }
       else
       {
          
           abort('401');
       }
	}
	
    private function delete_replace_locations(Request $request,$level,$department)
    {
        $replaced_locations = [];
        
        if(  $sub_locations = DB::table('delivery_locations')
            ->where('seller_company_id',$request->seller_company_id)
            ->where('department',str_replace(' ','_',$department))
            ->where($level,$request->$level)
            ->get(['country','county','county_l4'])
            ->toArray()
        
        )
        {
            
            DB::table('delivery_locations')
                ->where('seller_company_id',$request->seller_company_id)
                ->where('department',str_replace(' ','_',$department))
                ->where($level,$request->$level)
                ->delete();
            
           
            foreach($sub_locations as $location)
            {
                $replaced_locations[] = [
                    'country'       =>  LocationNameOrId::get_name($location->country) ,
                    'county'        =>  LocationNameOrId::get_name($location->county) ,
                    'county_l4'     =>  LocationNameOrId::get_name($location->county_l4) ,
                ];
            }
        }
        
        DB::table('delivery_locations')
            ->insert([
                'seller_id'         =>  \Auth::guard('seller')->user()->id,
                'seller_company_id' =>  $request->seller_company_id,
                'country'           =>  $request->country,
                'county'            =>  $request->county,
                'county_l4'         =>  $request->county_l4,
                'department'        =>  str_replace(' ','_',$department),
                'delivery_days'     =>  json_encode($request->delivery_days),
                'created_at'        =>  date('Y-m-d H:i:s'),
                'updated_at'        =>  date('Y-m-d H:i:s')
            ]);
    
       
        
        return $replaced_locations;
        
        
        
    }
    private function notification_for_sellers($seller_company_id,$subject = null)
    {
        if(!$subject)
            $subject   =   __('seller_name has updated delivery locations.',
                [
                    'seller_name'  =>   \Auth::guard('seller')->user()->name,
                ]);
        $details    =   [
            'n_link'                =>    '/delivery_locations',
            'seller_ids'            =>     $this->seller_ids($seller_company_id),
            'action'                =>    'delivery_locations_for_sellers',
            'seller_name'           =>    \Auth::guard('seller')->user()->name,
            'subject'               =>     $subject,
        ];
        
        SellerNotificationEvent::dispatch($details);
        ////UPDATE COMPANY IN SESSION
        $this->company->update('delivery_locations');
    }
    private function seller_ids($seller_company_id)
    {
      
        $seller_ids =  array_keys( $this->companies()[ $seller_company_id ]->staff_ids['seller_seller'] );
        
        $flipped    =   array_flip($seller_ids);
        /*NOT SENDING NOTIFICATION TO HIMSELF*/
        unset($flipped[\Auth::guard('seller')->user()->id]);
        
        $seller_ids =   array_flip($flipped);
        
        if(\Auth::guard('seller')->user()->role != 'seller_owner' )
        {
            array_push($seller_ids, $this->role->get_owner_id());
        }
        return $seller_ids;
    }
}
