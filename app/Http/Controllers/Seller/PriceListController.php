<?php

namespace App\Http\Controllers\Seller;

use App\Events\BuyerNotificationEvent;
use App\Events\SellerNotificationEvent;
use App\Services\ArrayIs;
use App\Services\HashMaker;
use App\Services\StrReplace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Converter;
use App\Services\Currency;
use App\Services\DeliveryDays;
use App\Services\Language;
use App\Services\LocationNameOrId;
use App\Services\MatchMaker;
use App\Services\PaymentFrequency;
use App\Services\PriceList;
use App\Services\Sanitizer;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Role;
use App\Repository\NavigationRepository;
use App\Jobs\ProductListEmailJob;
use App\Jobs\PriceListEmailJob;
use Illuminate\Support\Facades\Gate;
use App\Services\Company;

class PriceListController extends Controller
{
    
    public $role;
    public $company;
    public $deliveryDays;
    public $locationsId;
    
    public function __construct( Company $company, Role $role, NavigationRepository $navigationRepository,DeliveryDays $deliveryDays )
    {
       
        $this->navigationRepository = $navigationRepository;
        $this->role = $role;
        $this->company = $company;
        $this->deliveryDays = $deliveryDays;
        $this->middleware('seller.auth:seller');
     
    }
    public function companies()
    {
      // $this->company->update('cooperation_requests');
        return $this->company->for($this->role->get_owner_or_staff());
    }
    
    public function create($buyer_company_id,$department,$seller_company_id)
    {
      
        $price_list_seller_id = !isset( $this->companies()[session()->get('company_id')]->price_lists[$buyer_company_id][$department]['seller_id'])
          ? null :  $this->companies()[session()->get('company_id')]->price_lists[$buyer_company_id][$department]['seller_id'];
        
        /*SELLER COMPANY HAS COOPERATION REQUESTS HISTORY WITH BUYER COMPANY*/
        if(!$this->good_to_price($buyer_company_id,$department,$seller_company_id)
        /*STAFF SELLER CAN'T VIEW PRICE LIST NOT ASSIGNED TO HIM*/
        || $price_list_seller_id != null && $price_list_seller_id != \Auth::guard('seller')->user()->id
            && \Auth::guard('seller')->user()->role != 'seller_owner')
        {
            abort(404);
        }
      
       
        $info = $this->info($buyer_company_id,$department,$seller_company_id);
      
        if($info->sellers_extended_price_list == null )  return back()->with(['no_price_list'=>true]);
       
        $seller_price_list = $this->price_list( $info,$buyer_company_id,$department) ;
     
        return view('seller.price_list.create',
            compact('info','seller_price_list'
            ));
        
    }
    public function apply_default_prices()
    {
        
        if(!$this->good_to_price(
            session()->get('buyer_company_id'),
            session()->get('department'),
            session()->get('seller_company_id')))
            
            abort(404);
    
        $info = $this->info(
            session()->get('buyer_company_id'),
            session()->get('department'),
            session()->get('seller_company_id'));
        
        if($info->sellers_extended_price_list == null )  return back()->with(['no_price_list'=>true]);
       
        
       $seller_price_list       =   $this->price_list_prices_applied($info)->list;
       $seller_price_list_multi =   $this->price_list_prices_applied($info)->multi;
       
        $info->action   =  session()->get('action');
        $info->delivery_location_id   =  session()->get('delivery_location_id');
        
        return view('seller.price_list.prices_applied', compact(

            'info',
            'seller_price_list_multi',
            'seller_price_list'
           
        ));
    }
    public function store(Request $request)
    {
    
        $buyer_company_id   =   session()->get('buyer_company_id');
        $seller_company_id  =   session()->get('seller_company_id');
        $department         =   session()->get('department');
        $payment_frequency  =   $request->request->has('payment_frequency') ?
            $request->payment_frequency : session()->get('buyer_payment_frequency') ;
        $delivery_days      =   $request->request->has('delivery_days') ?
            $request->delivery_days : session()->get('buyer_delivery_days') ;
        $products           =   Sanitizer::do_strings($request->products,'price_list');
        $currency           =   $request->currency;
        $language           =   $request->language;
      $delivery_location_id =   session()->pull('delivery_location_id');
        $rate               =   Converter::get_rate($seller_company_id,session()->get('sc_preferred_currency'),$currency);
      
        $translation   =  $this->companies()[$seller_company_id]->price_lists_extended['price_list_translations'];
        
        if($language !=session()->get('sc_preferred_language'))
        {
            foreach($translation [$language] as $hash_name  =>  $data)
            {
                $translator[$data['product_name']] = $hash_name;
            }
        }
        
        $seller_company =$this->companies()[$seller_company_id] ;
    
        $buyer_company = $seller_company->buyer_companies[$buyer_company_id];
        
        foreach($products as $product   =>  $product_info)
        {
            if (isset($product_info['unset']))
            {
                $unset =  $product_info['unset'];
            }
            else{
                $unset =  null;
            }
            $product_info['box_price'] =   $product_info['box_price'] == '' ?   0 : $product_info['box_price'] ;
            
    
            /*HERE IF LANGUAGE IS NOT PREFERRED WE NEED TO TRANSLATE IT TO PREFERRED*/
            ///// HASHED NAME WITHOUT THE RATE TO BE CONSISTENT WITH THE PRODUCT NAME
            /// FOR FUTURE PRODUCTS UPDATES AND WITH ORIGINAL PRODUCT NAME IN PREFERRED LANGUAGE
          $orig_product_name    =    isset($translator) ? explode('+',$translator[$product_info['product_name']])[0]:
              $product_info['product_name'];
        
            $product_hash_name = HashMaker::product($orig_product_name,$product_info,$rate);
          
            /////BUYERS PRICE LIST
            $buyer_pl_hash[$product_hash_name] =[
                'product_name' => ($product_info['product_name']),
                'product_code' => ($product_info['product_code']),
                'price_per_kg' => ($product_info['price_per_kg']),
                'type_brand' => ($product_info['type_brand']),
                'box_size' => ($product_info['box_size']),
                'box_price' => ($product_info['box_price']),
                'additional_info' => ($product_info['additional_info']),
                'unset' => ($unset)
            ];
            ///// END OF BUYERS PRICE LIST
    
           
            if( $product_hash_name != str_replace('_',' ',$product_info['old_hash_name']) )
    
            {
                /// IF SELLER CHANGED SOME DATA IN ANY OF THE PRODUCTS
                /// WHILE PRICING FOR BUYER, WE WILL CATCH IT
                /// AND ADD IT TO EXTENDED PRICE LIST AS NEW PRODUCT
                /// AND WILL REDIRECT SELLER TO HIS EXTENDED PRICE LIST TO ADD STOCK LEVEL
                /// AND LOW STOCK WARNING AS THIS IS NEW PRODUCT
                /// AND WILL UPDATE BUYERS PRICE LIST AS WELL
                ///
                ///
              
                ///
                ///
                $new_additions[]    =  $product_hash_name;
                $hash_translations[$product_hash_name] = str_replace('_',' ',$product_info['old_hash_name']);
    
                /////SELLERS EXTENDED PRICE LIST
               
                $additions_to_extended_pl[$product_hash_name]  = [
                    'product_name' => $orig_product_name,
                    'product_code' => ($product_info['product_code']),
                    'price_per_kg' => ($product_info['price_per_kg']+ 0)*(1/$rate) ,///we are saving only default currency prices)
                    'stock_level' => '',
                    'low_stock' => '',
                    'type_brand' => ($product_info['type_brand']),
                    'box_size' =>  ($product_info['box_size']+0),
                    'box_price' => ($product_info['box_price']+ 0)*(1/$rate) ,///we are saving only default currency prices)
                    'additional_info' => ($product_info['additional_info']),
                    'unset' => ($unset) ];
    
             
                if($language !=session()->get('sc_preferred_language')){
                    $addition_to_translation[$product_hash_name]    =
                        $translation[$language][ StrReplace::currency_underscore($product_info['old_hash_name']) ];
                }
            }
            ///// END OF SELLERS EXTENDED PRICE LIST
        }
    
      //  dd($addition_to_translation,$translation);
        if(isset($addition_to_translation))
        {
            $translation[$language] = array_merge(  $translation[$language],$addition_to_translation);
          
            DB::table('price_list_translations')
                ->updateOrInsert(
                    [
                        'seller_id'=> Auth::guard('seller')->user()->id,
                        'seller_company_id'=> $seller_company_id,
                        'department' => $department ,
                        'deleted_at' => null
                    ],
                    [
                        'translations' => json_encode($translation),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
        
        }
        
        //// IF SELLER ADDED NEW PRICE OR SOME OTHER NEW DATA TO PRODUCT WE WILL
        ///  SAVE IT IN EXTENDED PRICE LIST FOR FUTURE USE
        if(isset($additions_to_extended_pl))
        {
            $extended_pl = $this->companies()[$seller_company_id]->price_lists_extended['price_lists_extended'][$department];
           
            
            $current_pl = $extended_pl == null ? [] : $extended_pl;
           
            $extended_pl_hash = array_merge( $current_pl,$additions_to_extended_pl);
  
            DB::table('price_lists_extended')
                ->where([
                    'department' => $department,
                    'seller_company_id' => $seller_company_id,
                    'deleted_at'=>null
                ])
                ->update([
                    'price_list' => json_encode($extended_pl_hash),
                    'updated_by_seller_id' =>  \Auth::guard('seller')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
        }
        ///// UPDATE OR INSERT INTO price_lists (BUYER'S PRICE LIST
       if(!DB::table('price_lists')
       ->where([
           'buyer_company_id'  =>  $buyer_company_id,
           'seller_company_id' =>  $seller_company_id,
           'department'        =>  $department ,
       ])
       ->update([
           'price_list'               =>  json_encode($buyer_pl_hash),
           'updated_at'               =>  date('Y-m-d H:i:s'),
           'updated_by_seller_id'     =>  \Auth::guard('seller')->user()->id
       ]))
       {
           DB::table('price_lists')
               ->insert([
                   'seller_id'         =>  \Auth::guard('seller')->user()->id,
                   'buyer_company_id'  =>  $buyer_company_id,
                   'seller_company_id' =>  $seller_company_id,
                   'department'        =>  $department ,
                   'country'           =>  $buyer_company['country'],
                   'county'            =>  $buyer_company['county'],
                   'county_l4'         =>  $buyer_company['county_l4'],
                   'currency'          =>  $currency,
                   'language'          =>  $language,
                   'delivery_location_id' =>  $delivery_location_id,
                   'payment_frequency'    =>  $payment_frequency,
                   'price_list'        =>  json_encode($buyer_pl_hash),
                   'delivery_days'     =>  json_encode($delivery_days),
                   'created_at'        =>  date('Y-m-d H:i:s'),
                   'updated_at'        =>  date('Y-m-d H:i:s')
               ]);
       }

//
        ///// UPDATE OR INSERT INTO product_list_updates (BUYER'S PRICE LIST)
        DB::table('product_list_updates')
            ->updateOrInsert(
                [
                    'buyer_company_id'  =>  $buyer_company_id,
                    'seller_company_id' =>  $seller_company_id,
                    'department'        =>  $department ,
                ],
                [
                    'seller_updated'    => 'yes',
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);
    
        /////  UPDATING product_list_requests TABLE WITH RESPONDER_USER_ID
        DB::table('product_list_requests')
            ->where( 'department', $department)
            ->where( 'buyer_company_id', $buyer_company_id)
            ->where( 'seller_company_id',  $seller_company_id)
            ->update([
                'responded' => 1,
                'responder_user_id' => \Auth::guard('seller')->user()->id,
                'responded_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        $details    =   [
            'n_link'                =>    '/price_list/'.$department.'/'.$seller_company_id.'/'.$buyer_company_id,
            'action'                =>   'price_list_updated',
            'buyer_owner_email'     =>  $buyer_company['buyer_owner_email'] ,
            'buyer_company_name'    =>  $buyer_company['company_name'],
            'seller_company_name'   =>  $seller_company->seller_company_name,
            'buyer_company_id'      =>  $buyer_company_id,
            'seller_company_id'     =>  $seller_company_id,
            'subject'               =>   __('seller_company has priced your products. Remember to activate seller_company from within your account, to start ordering from them.',
                [
                    'seller_company'  =>  $seller_company->seller_company_name
                ]),
            'message'               =>    __('seller_company has priced your products. Remember to activate seller_company from within your account, to start ordering from them.',
                [
                    'seller_company'  =>  $seller_company->seller_company_name
                ]),
           
        ];
        //// sending email to buyer owner
        // dispatch(new PriceListEmailJob($details));
        
        ///// PUSHER
        BuyerNotificationEvent::dispatch($details);
    
    
    
        $seller_id = null;
       
        $current_seller_id = isset($this->companies()[ $seller_company_id ]->price_lists[$buyer_company_id][$department]['seller_id'])
        ? $this->companies()[ $seller_company_id ]->price_lists[$buyer_company_id][$department]['seller_id']
            :   null;
        /*IF OWNER IS UPDATING PRICE LIST AND STAFF IS MANAGIING IT*/
       if($current_seller_id != \Auth::guard('seller')->user()->id)
       {
           $seller_id = $current_seller_id;
       }
    
           /*IF STAFF IS UPDATING PRICE LIST NOTIFY OWNER*/
       elseif ($current_seller_id == \Auth::guard('seller')->user()->id && \Auth::guard('seller')->user()->role != 'seller_owner' )
       {
           $seller_id   =   $this->role->get_owner_id();
       }
        /*IF STAFF IS CREATING PRICE LIST FOR THE FIRST TIME*/
     if(!$seller_id && \Auth::guard('seller')->user()->role != 'seller_owner')
     {
         $seller_id =    $this->role->get_owner_id();
     }
    
      if($seller_id )
      {
          /*NOTIFICATION FOR LOGGED IN COMPANY SELLERS*/
          $details_for_sellers    =   [
              'n_link'                =>    '/pricing/'.$buyer_company_id.'/'.$department.'/'.$seller_company_id,
              'seller_id'             =>    $seller_id,
              'action'                =>    'price_list_updated_for_sellers',
        
              'subject'               =>   __('seller_name has updated buyer_company_name prices.',
                  [
                      'seller_name'          =>   \Auth::guard('seller')->user()->name,
                      'buyer_company_name'   =>    $buyer_company['company_name'],
                  ]),
    
    
          ];
    
          SellerNotificationEvent::dispatch($details_for_sellers);
    
      }
        
       
        
        
        $this->company->update(['price_lists_extended','price_lists']);
        
        function flatten(array $array) {
            $return = array();
            array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
            return $return;
        }
        if(isset($new_additions))
        {
           
            session()->put('new_additions',$new_additions);
            return redirect()->action(
                'Seller\ExtendedPriceListController@create_extended_price_list',
                [   'language' => session()->get('sc_preferred_language'),
                    'department' => $department,
                    'currency' => session()->get('sc_preferred_currency'),
                    'new_additions' => true]
            );
        }
        
        return redirect()->action(
            'Seller\PriceListController@create',
            [   'seller_company_id' => $seller_company_id,
                'department' => $department,
                'buyer_company_id' => $buyer_company_id]
        );
    }
    
    private function good_to_price($buyer_company_id,$department,$seller_company_id)
    {
       
       return
          isset( $this->companies()
           [$seller_company_id]
           ->cooperation_requests
           [$buyer_company_id]
           [$department]
           ['seller']
           [1]
           [1] )
           ||
          isset( $this->companies()
           [$seller_company_id]
           ->cooperation_requests
           [$buyer_company_id]
           [$department]
           ['buyer']
           [1] ) ;
       
    }
    private function info($buyer_company_id,$department,$seller_company_id)
    {
        
        $prices_for_modal  =   [];
        $s_company = $company = $this->companies()[$seller_company_id] ;
        
        $b_company= $s_company->buyer_companies[$buyer_company_id];
        
        $delivery_days_info   =   $this->deliveryDays->delivery_days($s_company,$buyer_company_id,$department);
        $delivery_days        =   $delivery_days_info['delivery_days'];
        $delivery_days_type   =   $delivery_days_info['type'];
        
        $matches =   MatchMaker::find_match($buyer_company_id,$department,$b_company['languages']['preferred'],$s_company);
    
        $b_company['all_currencies'] = Currency::get_name_for_company( $matches['bc_currencies']['all']  );
        $b_company['all_languages'] = Language::names_for_company( $matches['bc_languages'] ) ;
        
        $s_company->all_currencies = Currency::get_name_for_company(  $matches['sc_currencies'] );
        $s_company->all_languages = Language::names_for_company(  $matches['sc_languages']) ;
        
        $full_path =    LocationNameOrId::path($b_company);
        $days   =   $this->role->days();
    
    
    
        $invoice_freq =  isset($s_company->price_lists[$buyer_company_id][$department]['payment_frequency']) ?
            $s_company->price_lists[$buyer_company_id][$department]['payment_frequency'] :
            $s_company->payment_method;
      
        $payment_frequency          =   PaymentFrequency::get();
        $buyer_payment_frequency    =   PaymentFrequency::get()[$invoice_freq];
    
    
    
        session(['sc_preferred_currency' =>  $s_company->currencies['preferred']]);
        session(['sc_preferred_language' => $s_company->languages['preferred']]);
        session(['seller_company_id' => $seller_company_id]);
        session(['department' => $department]);
        session(['buyer_company_id' => $buyer_company_id]);
        session(['buyer_payment_frequency' => $invoice_freq]);
        session(['buyer_delivery_days' => $delivery_days]);
        session(['delivery_days_for_buyers_location' => $delivery_days]);
    
    
        $buyers_product_lists = isset( $s_company->product_lists[$buyer_company_id][$department]) ?
            $s_company->product_lists[$buyer_company_id][$department]:    []  ;
    
        $sellers_extended_price_list   =  $s_company->price_lists_extended['price_lists_extended'][$department];
        
        $rate = Converter::get_rate($seller_company_id, $s_company->currencies['preferred'],$matches['currency']);
    
        $translation = [];
    
        if(  $matches['match'] != false )
        {
       // dd($s_company->price_lists_extended['price_list_translations']);
           // $sellers_extended_price_list_translations   =   $s_company->price_lists_extended['price_list_translations'][$department][$matches['language']];
            if(isset($s_company->price_lists_extended['price_list_translations'][$department][$matches['language']]))
            {
                $translation    =  $s_company->price_lists_extended['price_list_translations'][$department][$matches['language']];
            }

            $new_products   =
                PriceList::find_new_products( $buyers_product_lists[$matches['language']],  $sellers_extended_price_list,$translation);
            session()->put([
                
                    'currency'  =>  $matches['currency'],
                    'language'  =>  $matches['language'],
                    'department'=>  $department]
            );
        
        }
        else
        {
        
            $new_products   =
                PriceList::find_new_products($buyers_product_lists,  $sellers_extended_price_list,$translation);
        }
    
        if($new_products != [])
        {
            session()->put([
                    'new_products'=> $new_products,
                ]
            );
        }
    
    
        ///// NEED TO RESORT IT BECAUSE OF JSON COLUMN IN MYSQL SORTING
        /// FOR MODAL
        if(isset($sellers_extended_price_list))
        {
            foreach($sellers_extended_price_list as $long_name  =>  $data)
            {
                if(isset($translation[$long_name]['product_name']) )
                {
                    $prices_for_modal[$long_name]['product_name']  =  $translation[$long_name]['product_name'];
                    $prices_for_modal[$long_name]['product_code']  = $data['product_code'];
                    $prices_for_modal[$long_name]['price_per_kg']  = $data['price_per_kg']*$rate;
                    $prices_for_modal[$long_name]['type_brand']  =  $translation[$long_name]['type_brand'];
                    $prices_for_modal[$long_name]['stock_level']  = $data['stock_level'];
                    $prices_for_modal[$long_name]['box_size']  = $data['box_size'];
                    $prices_for_modal[$long_name]['box_price']  = $data['box_price']*$rate;
                    $prices_for_modal[$long_name]['additional_info']  =  $translation[$long_name]['additional_info'];
                    $prices_for_modal[$long_name]['unset']  = $data['unset'];
                }
                else
                {
                    $prices_for_modal[$long_name]['translation_needed']  =  1;
                    $prices_for_modal[$long_name]['product_name']  =  $data['product_name'];
                    $prices_for_modal[$long_name]['product_code']  = $data['product_code'];
                    $prices_for_modal[$long_name]['price_per_kg']  = $data['price_per_kg']*$rate;
                    $prices_for_modal[$long_name]['type_brand']  =  $data['type_brand'];
                    $prices_for_modal[$long_name]['stock_level']  = $data['stock_level'];
                    $prices_for_modal[$long_name]['box_size']  = $data['box_size'];
                    $prices_for_modal[$long_name]['box_price']  = $data['box_price']*$rate;
                    $prices_for_modal[$long_name]['additional_info']  =  $data['additional_info'];
                    $prices_for_modal[$long_name]['unset']  = $data['unset'];
                }
            
            }
        }
       
        $info = (object)  [
            'prices_in'                 =>   MatchMaker::prices_in($s_company),
            'department'                =>  $department,
            'prices_for_modal'          =>  $prices_for_modal,
            'new_products'              =>  $new_products,
            'buyer_payment_frequency'   =>  $buyer_payment_frequency,
            'payment_frequency'         =>  $payment_frequency,
            'full_path'                 =>  $full_path,
            'days'                      =>  $days,
            's_company'                 =>  $s_company,
            'b_company'                 =>  $b_company,
            'delivery_days'             =>  $delivery_days,
            'delivery_days_type'       =>  $delivery_days_type,
            'matches'                   =>  $matches,
            'rate'                      =>   $rate,
            'match'                     =>  $matches['match'],
            'buyers_product_lists'      =>  $buyers_product_lists,
            'delivery_location_id'      =>  $s_company->buyer_companies[$buyer_company_id]['delivery_locations'][$department],
            'invoice_freq'              =>  $invoice_freq,
            'sellers_extended_price_list' => $sellers_extended_price_list
        ];
       
        session()->put('delivery_location_id',$info->delivery_location_id);
        
        return $info;
    }
    private function price_list($info,$buyer_company_id,$department)
    {
        $price_list_for_buyer    =   isset( $info->s_company->price_lists[$department][$buyer_company_id]) ?
            $info->s_company->price_lists[$department][$buyer_company_id]  :   [];
   
        $price_list_details =   PriceList::get(
            $info->buyers_product_lists,
            $info->matches,
            $info->b_company['languages']['preferred'],
            $price_list_for_buyer );
       
        $info->action = $price_list_details['action'];
        $info->match = $price_list_details['match'];
      
        ///// NEED TO RESORT IT BECAUSE OF JSON COLUMN IN MYSQL SORTING
       return PriceList::re_sort_price_list( $price_list_details['price_list'] ) ;
    }
    private function price_list_prices_applied($info)
    {
        $rate = $info->rate ;
//
//        $sellers_extended_price_list_translations   =
//            $info->s_company->price_lists_extended['price_list_translations']
//            [session()->get('department')];
//
//        if(isset($sellers_extended_price_list_translations[$info->matches['language']]))
//        {
//            $translation    =   $sellers_extended_price_list_translations[$info->matches['language']];
//        }

        $translation = !isset( $info->s_company->price_lists_extended['price_list_translations']
            [session()->get('department')][$info->matches['language']]) ? [] :
                $info->s_company->price_lists_extended['price_list_translations']
                [session()->get('department')][$info->matches['language']];


        $buyers_default_product_list = $info->s_company->product_lists
        [session()->get('buyer_company_id')]
        [session()->get('department')]
        [session()->get('language')];
    
        $all_products = [];
        $multiples_array = [];
    
        foreach($info->sellers_extended_price_list as $long_name=>$product_info)
        {
            /*RATE IS 1 FOR PRODUCT HASH NAME TO BE CONSISTENT ALL AROUND*/
            $product_hash_name = HashMaker::product($product_info['product_name'],$product_info,1);
        
        
            /*WE WILL DISPLAY PRODUCTS IN MATCHED LANGUAGE*/
            if(session()->get('language') !=$info->s_company->languages['preferred'])
            {
            
                /*IF SELLER HAS PRODUCT TRANSLATION*/
                if(isset($translation[$long_name]['product_name']))
                {
                
                    $prices_for_modal[$product_hash_name]= [
                        'old_hash_name' => $product_hash_name,
                        'product_name' => $translation[$long_name]['product_name'],
                        'product_code' => $product_info['product_code'],
                        'price_per_kg' => $product_info['price_per_kg']*$rate,
                        'type_brand' => $translation[$long_name]['type_brand'],
                        'stock_level' => $product_info['stock_level'],
                        'box_size' => $product_info['box_size'],
                        'box_price' => $product_info['box_price']*$rate,
                        'additional_info' => $translation[$long_name]['additional_info'],
                        'unset' => $product_info['unset']];
                    ////// end for modal
                
                    $multiples_array[$translation[$long_name]['product_name']][]= [
                        'old_hash_name' => $product_hash_name,
                        'product_name' => $translation[$long_name]['product_name'],
                        'product_code' => $product_info['product_code'],
                        'price_per_kg' => $product_info['price_per_kg']*$rate,
                        'type_brand' => $translation[$long_name]['type_brand'],
                        'box_size' => $product_info['box_size'],
                        'box_price' => $product_info['box_price']*$rate,
                        'additional_info' =>$translation[$long_name]['additional_info'],
                        'unset' => $product_info['unset']];
                
                }
            }
            else
            {
            
                $prices_for_modal[ $product_hash_name ] = [
                    'old_hash_name' => $product_hash_name,
                    'product_name' => $product_info[ 'product_name' ],
                    'product_code' => $product_info[ 'product_code' ],
                    'price_per_kg' => $product_info[ 'price_per_kg' ] * $rate,
                    'type_brand' => $product_info[ 'type_brand' ],
                    'stock_level' => $product_info[ 'stock_level' ],
                    'box_size' => $product_info[ 'box_size' ],
                    'box_price' => $product_info[ 'box_price' ] * $rate,
                    'additional_info' => $product_info[ 'additional_info' ],
                    'unset' => $product_info[ 'unset' ] ];
                ////// end for modal
            
                $multiples_array[ $product_info[ 'product_name' ] ][] = [
                
                    'old_hash_name' => $product_hash_name,
                    'product_name' => $product_info[ 'product_name' ],
                    'product_code' => $product_info[ 'product_code' ],
                    'price_per_kg' => $product_info[ 'price_per_kg' ] * $rate,
                    'type_brand' => $product_info[ 'type_brand' ],
                    'box_size' => $product_info[ 'box_size' ],
                    'box_price' => $product_info[ 'box_price' ] * $rate,
                    'additional_info' => $product_info[ 'additional_info' ],
                    'unset' => $product_info[ 'unset' ] ];
            
            }
        }
    
        foreach($multiples_array as $product_name => $product_data_array)
        {
            if(in_array($product_name,array_values($buyers_default_product_list)))
            {
            
                if(sizeof($product_data_array) > 1 )
                {
                    $all_products[$product_name]  = $product_data_array;
                }
                else
                {
                    $all_products[$product_name]  = [
                        'old_hash_name' => $product_data_array[0]['old_hash_name'],
                        'product_name' => $product_data_array[0]['product_name'],
                        'product_code' => $product_data_array[0]['product_code'],
                        'price_per_kg' => $product_data_array[0]['price_per_kg'],
                        'type_brand' => $product_data_array[0]['type_brand'],
                        'box_size' => $product_data_array[0]['box_size'],
                        'box_price' => $product_data_array[0]['box_price'],
                        'additional_info' => $product_data_array[0]['additional_info'],
                        'unset' => $product_data_array[0]['unset']
                    ];
                }
            }
        }
    
        $seller_price_list_multi = [];
        $seller_price_list = [];
        foreach($all_products as $product_name_a => $product_data_a)
        {
            if(ArrayIs::multi($product_data_a) )
            {
            
                $seller_price_list_multi[$product_name_a] = $product_data_a;
            }
            else
            {
                $seller_price_list[$product_name_a] = $product_data_a;
            }
        }
        
        return $price_list = (object)   [
            'multi' =>  $seller_price_list_multi,
            'list'  =>  $seller_price_list
        ];
    }
}
