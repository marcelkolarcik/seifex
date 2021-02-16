<?php

namespace App\Http\Controllers;


use App\Events\BuyerNotificationEvent;
use App\Events\SellerNotificationEvent;
use App\Order;
use App\Services\LocationNameOrId;
use App\Services\Ordering;
use App\Services\Sanitizer;
use Illuminate\Http\Request;
use App\Repository\OrdersRepository;
use DB;
use Illuminate\Support\Facades\Auth;

use App\Services\Role;
use App\Jobs\OrderEmailJob;
use App\Repository\OrderingRepository;
use Illuminate\Support\Facades\Gate;
use App\Services\Company;




class OrdersController extends Controller
{
    
   
    public $orders_repository;
    public $role;
    public $company;
    public $orderingRepository;
    
	
	
	public function __construct(
                                
                                 OrdersRepository $ordersRepository,
                                 OrderingRepository $orderingRepository,
                                 Role $role,
                                 Company $company
                                   )
	{
        $this->company = $company;
		$this->orders_repository = $ordersRepository;
        $this->orderingRepository = $orderingRepository;
        $this->role = $role;
        $this->middleware('buyer_seller')->only([ 'display_order','order_delivered','order_dispatched']);
        $this->middleware('buyer.auth:buyer')->except(['orders', 'display_order','order_delivered','order_dispatched']);
        
	}
	
	private function buyer_companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
	public function order_dispatched( Request $request )
	{
	    $now = date('Y-m-d H:i:s');
	    
		if (DB::table('orders')
			->where('id', $request->order_id)
			->update([ 'prepped_at' => $now ]))
		{
		    
            $order =    [
                'n_link'                =>    '/order/'.$request->order_id.'/'.$request->buyer_company_id,
                'action'                =>   'order_dispatched',
                'id'                    =>   $request->order_id,
                'buyer_company_id'      =>   $request->buyer_company_id,
                'order_dispatched_at'   =>   $now,
                'buyer_email'           =>   $request->buyer_email,
                'seller_email'          =>   $request->seller_email,
                'buyer_company_name'    =>   $request->buyer_company_name,
                'seller_company_name'   =>   $request->seller_company_name,
                'department'            =>   $request->department,
                'subject'               =>   __('_department order id order_id from seller_company was dispatched.',
                                                [
                                                    __('_department')     =>  __(str_replace('_',' ',$request->department)),
                                                    'order_id'                  =>  $request->id,
                                                    'seller_company'            =>  $request->seller_company_name
                                                ]),
             
            ];
            // EMAIL
           //dispatch( new OrderEmailJob($order));
          
            //PUSHER
            BuyerNotificationEvent::dispatch($order);
           // App::setLocale( $this->browser_language->setBrowserMatch() );
            //dd(__('Send invoice to buyer ?'),$_SERVER['HTTP_ACCEPT_LANGUAGE'], App::getLocale(),$this->browser_language->setBrowserMatch());
			return ['status' => 'dispatched',
                    'title' => __('Great !'),
                    'text' => __('Order status is updated to dispatched.'),
                    'title_error' => __('Something went wrong.'),
                    'text_error' => __('Please try again later.')];
		}
	}
	
    public function order_delivered( Request $request )
    {
        $now = date('Y-m-d H:i:s');
        if (DB::table('orders')
            ->where('id', $request->order_id)
            ->update([ 'delivered_at' => $now ]))
        {
            
    
            $order =    [
                'n_link'                =>    '/order/'.$request->order_id.'/'.$request->buyer_company_id,
                'action'                =>   'order_delivered',
                'id'                    =>   $request->order_id,
                'buyer_company_id'      =>   $request->buyer_company_id,
                'order_delivered_at'    =>   $now,
                'buyer_email'           =>   $request->buyer_email,
                'seller_email'          =>   $request->seller_email,
                'buyer_company_name'    =>   $request->buyer_company_name,
                'seller_company_name'   =>   $request->seller_company_name,
                'department'            =>   $request->department,
                'subject'               =>     __('_department order id order_id from seller_company was delivered.',
                                                        [
                                                            __('_department')     =>  __(str_replace('_',' ',$request->department)),
                                                            'order_id'                  =>  $request->order_id,
                                                            'seller_company'            =>  $request->seller_company_name
                                                        ]),
                'confirm'                =>  __('please confirm !'),
            ];
           
            /// MAIL
            //dispatch( new OrderEmailJob($order));
    
            // PUSHER NOTIFICATION
            BuyerNotificationEvent::dispatch($order);
            return ['status' => 'delivered',
                'title' => __('Great !'),
                'text' => __('Order status is updated to delivered.'),
                'title_error' => __('Something went wrong.'),
                'text_error' => __('Please try again later.')];
        }
    }
    
    public function order_delivery_confirmed( Request $request )
    {
//        $validator = \Validator::make($request->all(), [
//            'comment' => 'string|max:255',
//
//        ]);
//
//        if ($validator->fails()) { dd('validator failed');
//            return back();
//
//        }
        $now = date('Y-m-d H:i:s');
        
        if (DB::table('orders')
            ->where('id', $request->order_id)
            ->update([ 'buyer_confirmed_delivery_at'    => $now,
                        'comment'                       =>  $request->comment]))
        {
    
            $order =    [
                'n_link'                        =>    '/order/'.$request->order_id.'/'.$request->seller_company_id,
                'action'                        =>   'order_delivery_confirmed',
                'id'                            =>   $request->order_id,
                'seller_company_id'             =>   $request->seller_company_id,
                'order_delivery_confirmed_at'   =>   $now,
                'buyer_email'                   =>   $request->buyer_email,
                'seller_email'                  =>   $request->seller_email,
                'buyer_company_name'            =>   $request->buyer_company_name,
                'seller_company_name'           =>   $request->seller_company_name,
                'department'                    =>   $request->department,
                'subject'                       =>    __('_department order id order_id was confirmed as delivered by buyer_company.',
                                                            [
                                                                __('_department')     =>  str_replace('_',' ',$request->department),
                                                                'order_id'                  =>  $request->order_id,
                                                                'buyer_company'             =>  $request->buyer_company_name
                                                            ]),
                
             
                'comment'                       =>   $request->comment,
            ];
            // MAIL
           //dispatch( new OrderEmailJob($order));
            
            //PUSHER NOTIFICATION
            SellerNotificationEvent::dispatch($order);
            return ['status' => 'buyer_confirmed_delivery',
                'title' => __('Great !'),
                'text' => __('Order is confirmed as delivered.'),
                'title_error' => __('Something went wrong.'),
                'text_error' => __('Please try again later.')];
        }
        
        
    }
	
	public function orders()
    {
       
//        $l_and_p    =   DB::table('locations2')
//           ->whereIn('country_id', LocationNameOrId::current_countries_id())
//
//            ->pluck('location_id','location_name')->toArray();
//           // ->get(['location_name','location_id','path'])->toArray();
//
////        foreach($l_and_p as $key => $data)
////        {
////            $name_and_path[$data->location_id]['location_name']    =   $data->location_name;
////            $name_and_path[$data->location_id]['path']    =   $data->path;
////        }
//
//        file_put_contents('list.txt', var_export( $l_and_p, true));
//
//
//     dd('done');

        

       
        $companies                  =   $this->buyer_companies();
        $buyer_company_name         =   '';
        if($this->role->get_guard() === 'buyer')
        {
            $buyer_company_name         =  $this->buyer_companies()[session()->get('company_id')]->buyer_company_name;
        }
        
        $orders                     =   $this->orders_repository->orders();
        $ow_company_id              =   $this->role->company_id();
        $opposite_company_name      =   $this->role->opposite_company_name();
        $type                       =   $this->role->partial_role();
   
        ///// iIF THERE IS NO DEPARTMENT COMPANY => DEPARTMENT BECOMES COMPANY ID SO RETURN IT BACK TO ORIGINAL
       
        $orders_active = 'active';
        
        return view($this->role->partial_role().'.orders.orders', compact(
            'ow_company_id',
            'orders',
            'companies',
            'orders',
            'opposite_company_name',

            'orders_active','type',
            'buyer_company_name'));
    
    }
         
	public function display_order($order_id)
    {
        $order_id = Sanitizer::do_strings([$order_id]);
      
        if($this->role->partial_role() == 'seller')
            DB::table('orders')
            ->where('id', $order_id)
            ->update([ 'checked_at' => date('Y-m-d H:i:s') ]);
        
        $order = DB::table('orders')
            ->where('orders.id',$order_id)
            ->join('buyer_companies','buyer_companies.id','=','orders.buyer_company_id')
            ->join('seller_companies','seller_companies.id','=','orders.seller_company_id')
            ->get(['orders.*',
                'buyer_companies.buyer_name',
                'buyer_companies.buyer_phone_number',
                'buyer_companies.buyer_company_name',
                'buyer_companies.buyer_email',
                'seller_companies.seller_name',
                'seller_companies.seller_phone_number',
               
                'seller_companies.seller_email',
                'seller_companies.seller_company_name'])
            ->first()  ;
       
        if($order == null)  abort('404');
     
        return view($this->role->partial_role().'.orders.view_order', compact('order'));
    }
    
	
}
