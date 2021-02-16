<?php

namespace App\Http\Controllers;


use App\Events\BuyerNotificationEvent;

use App\Events\SellerNotificationEvent;
use App\Services\Converter;
use App\Services\Currency;
use App\Services\DeliveryDays;
use App\Services\Language;
use App\Services\LocationNameOrId;
use App\Services\MatchMaker;
use App\Services\PaymentFrequency;
use App\Services\PriceList;
use App\Services\Sanitizer;
use App\Services\StrReplace;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

use App\Services\Role;
use App\Repository\NavigationRepository;
use App\Jobs\ProductListEmailJob;
use App\Jobs\PriceListEmailJob;
use App\Repository\LocationIdRepository;
use Illuminate\Support\Facades\Gate;
use App\Services\Company;

class ProductListController extends Controller
{
    public $role;
    public $company;
    public $deliveryDays;
   
    
    public function __construct( Role $role,
                                 NavigationRepository $navigationRepository,
                                 DeliveryDays $deliveryDays,
                                
                                 Company $company)
    {
        
        $this->company = $company;
        $this->navigationRepository = $navigationRepository;
        $this->role = $role;
        $this->deliveryDays = $deliveryDays;
        $this->middleware('seller.auth:seller')->except(['product_list_request', 'cooperation_requests']);
        $this->middleware('buyer_seller')->only(['product_list_request', 'cooperation_requests']);
    }
   
  
    private function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
	///// COMMUNICATION BETWEEN BUYER AND SELLER
    public function product_list_request(Request $request)
    {
    
        $buyer_details   =   DB::table('buyer_companies')
            ->where('id',$request->buyer_company_id)
            ->get(['buyer_owner_email','buyer_company_name','country','county','county_l4'])
            ->first();


        $seller_details   =   DB::table('seller_companies')
            ->where('id',$request->seller_company_id)
            ->get(['seller_owner_email','seller_company_name'])
            ->first();
        
//        if($this->role->get_guard() == 'buyer') {
//            $buyer_details = $this->companies()[ $request->buyer_company_id ];
//            // dd( $buyer_details->seller_companies);
//            $seller_details = $buyer_details->seller_companies[ $request->seller_company_id ];
//        }
//        elseif($this->role->get_guard() == 'seller'){
//            $seller_details = $this->companies()[$request->seller_company_id];
//            $buyer_details = $seller_details->buyer_companies[$request->buyer_company_id];
//        }
        if(\Auth::guard('buyer')->user())
        {
            $seller_id = null;
        }
        elseif(\Auth::guard('seller')->user())
        {
            $seller_id = \Auth::guard('seller')->user()->id;
        }
       
    ///// BUYER IS RESPONDING TO SELLER'S REQUEST
    if(! DB::table('product_list_requests')
	    ->where( 'department', $request->department)
	    ->where( 'buyer_company_id', $request->buyer_company_id)
	    ->where( 'seller_company_id',  $request->seller_company_id)
        ->where( 'guard',   $this->role->get_opposite_guard())
	    ->update([
	                'responded' => 1,
                    'responder_user_id' => \Auth::guard($this->role->get_guard())->user()->id,
                    'responded_at' => date('Y-m-d H:i:s'),
				    'updated_at' => date('Y-m-d H:i:s'),
				    ])
    )
	    {
         
            ///// BUYER /SELLER IS SENDING PRODUCT LIST/REQUEST TO BUYER/SELLER
		    DB::table('product_list_requests')
			    ->insert([
				    'buyer_company_id'  => $request->buyer_company_id,
				    'seller_company_id' => $request->seller_company_id ,
                    'seller_id'         => $seller_id ,
                    'delivery_location_id' => $request->delivery_location_id ,
				    'department'        => $request->department,
				    'country'           => $buyer_details->country,
				    'county'            => $buyer_details->county,
				    'county_l4'         => $buyer_details->county_l4,
				    'created_at'        => date('Y-m-d H:i:s'),
				    'updated_at'        => date('Y-m-d H:i:s') ,
				    'requested'         => 1,
				    'requester'         => \Auth::guard($this->role->get_guard())->user()->role,
                    'guard'             =>  $this->role->get_guard(),
                    'requester_user_id' => \Auth::guard($this->role->get_guard())->user()->id,
			    ]) ;
        
        }
	 
        if($this->role->get_guard() == 'buyer')
        {
           
            
            $details    =   [
                'n_link'                =>    '/pricing/'. $request->buyer_company_id.'/'. $request->department.'/'. $request->seller_company_id,
                'action'                =>   'cooperation_requests',
                'owner_email'           =>  $seller_details->seller_owner_email ,
                'owner_company_name'    =>  $seller_details->seller_company_name,
                'activator_company_name'=>  $buyer_details->buyer_company_name,
                'subject'               =>   __('activator_company made product list available for pricing.',
                                                    [
                                                        'activator_company'=>$buyer_details->buyer_company_name
                                                    ]),
                'activator'             =>  'buyer',
                'department'            =>  $request->department,
                'buyer_company_id'      =>  $request->buyer_company_id,
                'seller_company_id'     =>  $request->seller_company_id ,
            ];
            
    
            //// PUSHER
            SellerNotificationEvent::dispatch($details);
        }
        if($this->role->get_guard() == 'seller')
        {
    
           
            
            
            $details    =   [
                'n_link'                =>    '/requests',
                'action'                =>   'cooperation_requests',
                'owner_email'           =>  $buyer_details->buyer_owner_email ,
                'owner_company_name'    =>  $buyer_details->buyer_company_name,
                'activator_company_name'=>  $seller_details->seller_company_name,
                'subject'               =>   __('activator_company requested your product list.',
                                                    [
                                                        'activator_company'=>$seller_details->seller_company_name
                                                    ]),
                'activator'             =>  'seller',
                'department'            => $request->department,
                'buyer_company_id'      => $request->buyer_company_id,
                'seller_company_id'     => $request->seller_company_id ,
               
            ];
            
    
            //// PUSHER
            BuyerNotificationEvent::dispatch($details);
        }
       
      //
         $this->company->update(['product_lists','buyer_companies','seller_companies','cooperation_requests']);
        ////EMAIL
      // dispatch(new ProductListEmailJob($details));
    
    
    
        return 'updated';
       
    }
    ///// COOPERATION REQUESTS => DISPLAY OF REQUESTS
    public function cooperation_requests()
    {
        $companies                  =   $this->companies();
        
        $product_list_requests      =   $this->navigationRepository->product_list_requests();
        $user_names                 =   $this->navigationRepository->user_names();
      // dd($product_list_requests,$user_names);
        $product_lists_active = 'active';
        
        return view($this->role->partial_role().'.product_list.product_list_requests', compact(
            'user_names',
            /*'company_names',*/
            'orders',
            'companies',
            'product_list_requests',
            'product_lists_active',
            'company_id',
            'department'
           ));
    
         }
    
}
