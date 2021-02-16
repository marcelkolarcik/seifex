<?php

namespace App\Http\Controllers\Buyer;

use App\Events\SellerNotificationEvent;
use App\Http\Controllers\Controller;
use App\Repository\OrderingRepository;
use App\Repository\OrdersRepository;
use App\Services\Company;
use App\Services\Currency;
use App\Services\NextDays;
use App\Services\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderingController extends Controller
{
    public $nextDays;
    public $orderingRepository;
    public $ordersRepository;
    public $role;
    public $company;
    
    public function __construct( NextDays $nextDays,
                                 Role $role,
                                 OrderingRepository $orderingRepository,
                                 OrdersRepository $ordersRepository,
                                 Company $company)
    {
        $this->nextDays             =   $nextDays;
        $this->company              =   $company;
        $this->role                 =   $role;
        $this->orderingRepository   =   $orderingRepository;
        $this->orders_repository    =   $ordersRepository;
   }
   private function buyer_companies()
   {
       return   $this->company->for($this->role->get_owner_or_staff());
   }
   
    public function ordering(  )
    {
        
        $next_days  =   $this->nextDays->get(7);
       
        $companies    =   $this->buyer_companies();
     
        if($companies[session()->get('company_id')]->price_lists === null)
            return view('buyer.ordering.ordering', compact('companies'));
       
        foreach($companies as $company)
        {
            $bc_names[$company->id]=$company->buyer_company_name;
            $bc_emails[$company->id]=$company->buyer_owner_email;
        }
        
        session()->put('bc_names',$bc_names);
        session()->put('bc_emails',$bc_emails);
       
        return view('buyer.ordering.ordering', compact('companies','next_days'
        /*'buyer_companies','num_of_departments','department','company_id'*/));
   }
    public function sellers_by_delivery_day( Request $request )
    {
       
        session()->put('bc_id',$request->buyer_company_id);
     
        $seller_price_lists    =  !isset($this->buyer_companies()[$request->buyer_company_id]->price_lists[$request->department])  ? [] :
            $this->buyer_companies()[$request->buyer_company_id]->price_lists[$request->department] ;
    
        $sellers_with_delivery_days_for_buyer_ids   =   [];
        $sellers_by_currency_language               =   [];
        
         foreach ($seller_price_lists as $id =>  $price_list)
         {
           
             if( $price_list->activated_by_buyer     ==  1    &&
                 $price_list->activated_by_seller    ==  1    &&
                 $price_list->department             ==  $request->department &&
                 $price_list->buyer_company_id       == $request->buyer_company_id &&
                 in_array($request->day_num,$price_list->delivery_days)  )
             {
                 $sellers_with_delivery_days_for_buyer_ids[$price_list->seller_company_id] = $price_list;
                 $sellers_by_currency_language[$price_list->currency][$price_list->language][$price_list->price_list_id] = $price_list;
             }
         }
       
       
        $b_c_preferred_currency = $this->buyer_companies()[$request->buyer_company_id]->currencies['preferred'];
         session()->put('b_c_preferred_currency',$b_c_preferred_currency);
        $b_c_preffered_language =   $this->buyer_companies()[$request->buyer_company_id]->languages['preferred'];
   
        $alternative    =   [];
        $price_list_ids =   [];
        
        if(!isset($sellers_by_currency_language[$b_c_preferred_currency][$b_c_preffered_language]) && $sellers_with_delivery_days_for_buyer_ids)
        {
            foreach($sellers_by_currency_language as $currency  =>  $languages)
            {
                foreach($languages as $language => $companies)
                {
                    $alternative[$currency][] = $language;
                    foreach($companies as $company)
                    {
                       
                        $price_list_ids[$currency][$language][]   =  $company->price_list_id;
                    }
                    
                }
               
            }
           // dd($alternative,$price_list_ids);
            session()->put('alternative_order_options',$alternative);
            session()->put('alternative_price_list_ids',$price_list_ids);
    
            $details    =   [
                'price_list_ids'    =>      $price_list_ids,
                'seller_ids'        =>      array_keys($sellers_with_delivery_days_for_buyer_ids),
                'buyer_company_id'  =>      $request->buyer_company_id,
                'department'        =>      $request->department,
                'en_timestamp'      =>      $request->en_timestamp,
                'day_num'           =>      $request->day_num];
    
            session([
                'details' => $details
            ]) ;
           
            return [
                'status'              =>  'no_preferred_sellers',
                'title'               =>  __('No sellers available for your preferred currency and language.'),
                'text'                =>  __('See available options!'.json_encode($alternative)),
            ];
        }
        elseif($sellers_with_delivery_days_for_buyer_ids === [])
        {
            return [
                'status'              =>  'no_sellers',
                'title'               =>  __('No sellers available.'),
                'text'                =>  __('Try different day / department!'),
            ];
        }
        else{
            $details    =   [
                'price_list_ids'    =>      $price_list_ids,
                'seller_ids'        =>      array_keys($sellers_with_delivery_days_for_buyer_ids),
                'buyer_company_id'  =>      $request->buyer_company_id,
                'department'        =>      $request->department,
                'en_timestamp'      =>      $request->en_timestamp,
                'day_num'           =>      $request->day_num];
    
            session([
                'details' => $details
            ]) ;
            return [
                'status'              =>  'sellers',
            ];
        }
       
    }
    public function load_form( Request $request )
    {
        
    
      
        $currency =   Currency::add_data_to_currency([session()->pull('b_c_preferred_currency')],'short_long');
        session()->put('order_currency',$currency);
        
        if($request->request->has('currency')) {
            $currency = Currency::add_data_to_currency([$request->currency],'short_long') ;
            session()->put('order_currency',$currency);
        }
        $language = '';
        if($request->request->has('language')) {
            $language = $request->language ;
            session()->put('order_language',$language);
        }
        
        $cheap_products             =   $this->orderingRepository->display_online_form(
            session('details')['department'],
            session('details')['buyer_company_id'],
            session('details')['seller_ids'])
        ['cheap_products'] ;
        
        $unavailable_products       =   $this->orderingRepository->display_online_form(
            session('details')['department'],
            session('details')['buyer_company_id'],
            session('details')['seller_ids'])
        ['unavailable_products'] ;
        
        $seller_companies       =   [];
        $seller_companies_all   =   $this->buyer_companies()[  session('details')['buyer_company_id']]->seller_companies;
        $price_lists            =   $this->buyer_companies()[  session('details')['buyer_company_id']]->price_lists;
      
        foreach($seller_companies_all as $id => $company)
        {
            if(in_array($id,session('details')['seller_ids']))
            {
                if($price_lists[session('details')['department']][$id]->buyer_company_id == session('details')['buyer_company_id'] &&
                    $price_lists[session('details')['department']][$id]->seller_company_id == $id )
                {
                 
                    $company['delivery_days'] =
                        $this->buyer_companies()
                        [  session('details')['buyer_company_id']]
                            ->sellers_delivery_days
                        [$id]
                        [session('details')['department']];
                    
                    $seller_companies[]  =   $company;
                   
                }
              
               
            }
        }
        
        
       $department =    session('details')['department'];
      
        return view('buyer.ordering.form',compact('seller_companies',
            'cheap_products',
            'unavailable_products',
            'language',
            'currency',
            'department'));
    }
    public function load_alternative()
    {
        $alternatives           =   session()->pull('alternative_order_options');
        $price_list_ids         =   session()->pull('alternative_price_list_ids');
      
        return view('buyer.ordering.includes.alternatives', compact('alternatives','price_list_ids'));
    }
    public function place_order(Request $request)
    {
    
        $orders             =   $request->products;
    
        $department         =   $request->department;
        $bc_id              =   session()->pull('bc_id');
       
        session()->put('company_id',$bc_id);
        $buyer_company_name =   session()->pull('bc_names')[ $bc_id ];
        $buyer_owner_email  =   session()->pull('bc_emails')[ $bc_id ];
        $sc_ids             =   array_keys($orders);
        $products_ordered_not_available = [];
    
        
        $seller_details=[];
       
        $seller_companies   =     $this->buyer_companies()[$bc_id]->seller_companies;
        $payment_frequency   =    $this->buyer_companies()[$bc_id]->sellers_payment_frequency;
        
        foreach($seller_companies as $id =>$seller_company)
        {
           
            if(in_array($id,$sc_ids))
                $seller_details[$id] = (object)
            [
                'seller_name'           =>      $seller_company['seller_name'],
                'seller_email'          =>      $seller_company['seller_email'],
                'seller_phone_number'   =>      $seller_company['seller_phone_number'],
                'seller_company_name'   =>      $seller_company['company_name'],
                'seller_owner_email'    =>      $seller_company['seller_owner_email'],
                'sc_id'                 =>      $id,
                'payment_method'        =>      $payment_frequency[$id][$department],
                'price_list'            =>      $this->buyer_companies()[$bc_id]->seller_price_lists_extended[$id][$department],
            ];
        }
        
        ////  HERE IN A LOOP DO ALL OF BELLOW FOR EACH OF SELLER COMPANIES AND THAT'S IT......
        ///
      
        $full_order = [];
        
        foreach($orders as $sc_id  =>  $order)
        {
          
            $seller_id = $this->buyer_companies()[$bc_id]->price_lists[$department][$sc_id]->seller_id;
            $del_loc_id = $this->buyer_companies()[$bc_id]->price_lists[$department][$sc_id]->delivery_location_id;
          
            $total_order_cost = 0;
            $products_ordered = [];
    
          
            foreach ($order as $product => $details)
            {
              
                if ($details['amount'] != null) {
                    $full_order[ str_replace('_', ' ', $product) ][ 'amount' ] = $details['amount'];
                }
            
                if ($details[ 'box_size' ] != 0 && $details['amount'] != null)
                {
                    $products_ordered [ str_replace('_', ' ', $product) ] =     $details['amount'];
    
                    $full_order[ str_replace('_', ' ', $product) ][ 'box_size' ] = $details[ 'box_size' ];
                    $full_order[ str_replace('_', ' ', $product) ][ 'product_code' ] = $details[ 'product_code' ];
                    $full_order[ str_replace('_', ' ', $product) ][ 'price_per_kg' ] = $details[ 'price_per_kg' ];
                    $full_order[ str_replace('_', ' ', $product) ][ 'type_brand' ] = $details[ 'type_brand' ];
                    $full_order[ str_replace('_', ' ', $product) ][ 'total_product_price_box' ] =
                        (float)$details['amount']
                        *
                        (float)$details[ 'box_size' ]
                        *
                        $details[ 'price_per_kg' ];
                
                    $total_order_cost += $full_order[ str_replace('_', ' ', $product) ][ 'total_product_price_box' ];
                
                }
                elseif ($details['amount'] != null)
                {
                    $products_ordered [ str_replace('_', ' ', $product) ] =     $details['amount'];
    
                    $full_order[ str_replace('_', ' ', $product) ][ 'product_code' ] = $details[ 'product_code' ];
                    $full_order[ str_replace('_', ' ', $product) ][ 'price_per_kg' ] = $details[ 'price_per_kg' ];
                    $full_order[ str_replace('_', ' ', $product) ][ 'type_brand' ] = $details[ 'type_brand' ];
                    $full_order[ str_replace('_', ' ', $product) ][ 'total_product_price' ] =
                        (float)$details['amount']
                        *
                        $details[ 'price_per_kg' ];
                
                    $total_order_cost += $full_order[ str_replace('_', ' ', $product) ][ 'total_product_price' ];
                }
            
            }
       
            foreach ($products_ordered as $product => $amount_ordered) {
               
                //// ADJUSTING STOCK LEVEL ACCORDING TO SALES
                /// IF SELLER HAS PRODUCT IN STOCK
                $seller_price_list =  $seller_details[$sc_id]->price_list;
                $hash_name  =   $orders[$sc_id][$product]['hash_name'];
              
                if (floatval($seller_price_list[ $hash_name ][ 'stock_level' ]) >= floatval($amount_ordered)) {
                    $seller_price_list[ $hash_name ][ 'stock_level' ] = floatval($seller_price_list[ $hash_name ][ 'stock_level' ]) - floatval($amount_ordered);
                }
               
                /// IF SELLER DOES NOT HAVE PRODUCT IN STOCK
                /// UNSET THE PRODUCT FROM THE ORDER
                else {
                    $products_ordered_not_available[] = $product;
                
                    if (isset($order[$product ][ 'total_product_price_box' ])) {
                        $total_order_cost -= $full_order[ $product ][ 'total_product_price_box' ];
                    }
                    if (isset($order[ $product ][ 'total_product_price' ])) {
                        $total_order_cost -= $full_order[ $product ][ 'total_product_price' ];
                    }
                
                    unset($order[ $product ]);
                }
            
            }
       
            $this->orders_repository->record_orders($full_order,$sc_id,$bc_id,$department,$total_order_cost);
           
            $invoice_freq = $this->buyer_companies()[$bc_id]->sellers_payment_frequency[$sc_id][$department];
            
            //// ADJUSTING STOCK LEVEL ACCORDING TO SALES
            ///   ///// HERE GET CURRENCY / LANGUAGE VERSION OF PRICE LIST IN FOREACH LOOP
            DB::table('price_lists_extended')
                ->where('seller_company_id', $sc_id)
                ->where('department', $department)
                ->update([ 'price_list' => json_encode($seller_price_list),
                    'updated_at' => date('Y-m-d H:i:s') ]);
        
            //// ORDER
       
            for($a=1;$a<15;$a++)
            {
                $this->orders_repository->record_orders($full_order,$sc_id,$bc_id,$department,$total_order_cost);
                DB::table('orders')
                    ->insertGetId([
                        'buyer_id'              =>      Auth::guard($this->role->get_guard())->user()->id,
                          'delivery_location_id'  =>      $del_loc_id,
                        'seller_id'             =>     $seller_id,
                        'buyer_company_id'      =>      $bc_id,
                        'seller_company_id'     =>      $sc_id,
                        'total_order_cost'      =>      $total_order_cost,
                        'buyer_company_name'    =>      $buyer_company_name,
                        'seller_company_name'   =>      $seller_details[$sc_id]->seller_company_name,
                        'department'            =>      $department,
                        'order'                 =>      json_encode($full_order),
                        'not_available'         =>      json_encode($products_ordered_not_available),
                        'created_at'            =>      date('Y-m-d H:i:s', strtotime("-".$a." months")),
                        'updated_at'            =>      date('Y-m-d H:i:s', strtotime("-".$a." months")),
                        'invoice_freq'          =>      $invoice_freq,
                        'currency'              =>      array_key_first(session()->get('order_currency'))]);
            }
            for($a=12;$a<25;$a++)
            {
                $this->orders_repository->record_orders($full_order,$sc_id,$bc_id,$department,$total_order_cost);
                DB::table('orders')
                    ->insertGetId([
                        'buyer_id'              =>      Auth::guard($this->role->get_guard())->user()->id,
            'delivery_location_id'  =>      $del_loc_id,
                        'seller_id'             =>     $seller_id,
                        'buyer_company_id'      =>      $bc_id,
                        'seller_company_id'     =>      $sc_id,
                        'total_order_cost'      =>      $total_order_cost,
                        'buyer_company_name'    =>      $buyer_company_name,
                        'seller_company_name'   =>      $seller_details[$sc_id]->seller_company_name,
                        'department'            =>      $department,
                        'order'                 =>      json_encode($full_order),
                        'not_available'         =>      json_encode($products_ordered_not_available),
                        'created_at'            =>      date('Y-m-d H:i:s', strtotime("-".$a." days")),
                        'updated_at'            =>      date('Y-m-d H:i:s', strtotime("-".$a." days")),
                        'invoice_freq'          =>      $invoice_freq,
                        'currency'              =>      array_key_first(session()->get('order_currency'))]);
            }

            $order_placed_at =   date('Y-m-d H:i:s');
            $order_id =  DB::table('orders')
                ->insertGetId([
                    'buyer_id'              =>      Auth::guard($this->role->get_guard())->user()->id,
                    'seller_id'             =>      $seller_id,
                    'delivery_location_id'  =>      $del_loc_id,
                    'buyer_company_id'      =>      $bc_id,
                    'seller_company_id'     =>      $sc_id,
                    'total_order_cost'      =>      $total_order_cost,
                    'buyer_company_name'    =>      $buyer_company_name,
                    'seller_company_name'   =>      $seller_details[$sc_id]->seller_company_name,
                    'department'            =>      $department,
                    'order'                 =>      json_encode($full_order),
                    'not_available'         =>      json_encode($products_ordered_not_available),
                    'created_at'            =>      $order_placed_at,
                    'updated_at'            =>      $order_placed_at,
                    'invoice_freq'          =>      $invoice_freq,
                    'currency'              =>      array_key_first(session()->get('order_currency'))
                ]);
        
        
        
            $details =    [
                'time'                  =>   $order_placed_at,
                'id'                    =>   $order_id,
                'seller_owner_email'    =>   $seller_details[$sc_id]->seller_owner_email,
                'buyer_owner_email'     =>   $buyer_owner_email,
                'buyer_company_name'    =>   $buyer_company_name,
                'seller_company_name'   =>   $seller_details[$sc_id]->seller_company_name,
                'department'            =>   $department,
                'order'                 =>   $full_order,
                'subject'               =>  __('New order id order_id.',['order_id'=>$order_id]),
                'subject_seller'        =>   __('New department order id order_id from buyer_company was placed.',
                    [
                        'department'=>str_replace('_',' ',$department),
                        'order_id'=>$order_id,
                        'buyer_company'=>$buyer_company_name
                    ]),
                'subject_buyer'        =>   __('You have placed new department order id order_id with seller_company.',
                    [
                        'department'    =>str_replace('_',' ',$department),
                        'order_id'      =>$order_id,
                        'seller_company'=>$seller_details[$sc_id]->seller_company_name
                    ]),
                'action'                =>   'order_placed',
            ];
        
            $order_details =    [
                'n_link'                =>   '/order/'.$order_id.'/'.$sc_id,
                'action'                =>  'order_placed',
                'time'                  =>   $order_placed_at,
                'id'                    =>   $order_id,
                'buyer_company_name'    =>   $buyer_company_name,
                'seller_company_id'     =>   $sc_id,
                'department'            =>   $department,
                'subject'               =>   __('New _department order id order_id from buyer_company was placed.',
                    [
                        __('_department')     =>  __(str_replace('_',' ',$department)),
                        'order_id'                  =>  $order_id,
                        'buyer_company'             =>  $buyer_company_name
                    ]),
               
            ];
        
            /////Email
            // dispatch( new OrderEmailJob($details));
        
        
            /////PUSHER
        
           SellerNotificationEvent::dispatch($order_details);
            
        }
        
        return redirect('/orders');
    }
}
