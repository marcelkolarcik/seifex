<?php

namespace App\Http\Controllers\Seller;

use App\BuyerCompany;
use App\Events\BuyerNotificationEvent;
use App\Events\SellerNotificationEvent;
use App\Services\ArrayIs;
use App\Services\DeliveryDays;
use App\Services\Departments;
use App\Services\HashMaker;
use App\Services\MatchMaker;
use App\Services\Sanitizer;
use App\Services\StrReplace;
use Illuminate\Http\Request;
use DB;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\DefaultDepartment;
use App\Services\Role;
use App\PriceList;
use Illuminate\Support\Facades\Validator;
use Mavinoo\LaravelBatch\Batch;
use App\Services\Currency;
use App\Services\Converter;
use App\Services\Language;
use App\Services\Company;

class ExtendedPriceListController extends Controller
{

    public $role;
    public $batch;
    public $currency;
    public $converter ;
    public $language ;
    public $departments ;
    public $company ;
    
    public function __construct(Role $role,
                                Batch $batch,
                                Currency $currency,
                                Converter $converter,
                                Language $language,
                                Departments $departments,
                                Company $company)
    {
        $this->role  = $role;
        $this->departments  = $departments;
        $this->batch  = $batch;
        $this->currency  = $currency;
        $this->converter  = $converter;
        $this->language  = $language;
        $this->company  = $company;
        
        $this->middleware('seller.auth:seller');
    }
   
    private function companies()
    {
        $this->company->update('staff');
        return $this->company->for($this->role->get_owner_or_staff());
    }
   
    public function create_extended_price_list(Request $request)
    
    {
       
        
     // $this->company->update('price_lists_extended');
        $department             =   str_replace(' ','_',$request->department);
        $seller_company_id      =   isset($request->seller_company_id) ? $request->seller_company_id : session()->get('company_id') ;
        $company                =   $this->companies()[$seller_company_id];
        $prices_in              =   MatchMaker::prices_in($company);
        $conversion_rates       =   $company->price_lists_extended['conversion_rates'];
        $rates                  =   $conversion_rates == null ? [] : $conversion_rates ;
        $c_preferred_language   =   $company->languages['preferred'];
        $c_all_languages        =   $company->languages['all'];
        $c_preferred_currency   =   $company->currencies['preferred'];
        $c_all_currencies       =   $company->currencies['all'];
        $language               =   isset($request->language)   ? $request->language : $c_preferred_language;
        $currency               =   isset($request->currency)   ? $request->currency : $c_preferred_currency;
        
        session()->put('c_preferred_currency', $c_preferred_currency );
        session()->put('c_preferred_language', $c_preferred_language );
        session()->put('rates',$rates);
        session()->put('prices_in',$prices_in);
    
        /// IF SELLER IS PRICING PRODUCT LIST FROM BUYER AND HE DOESN'T LANGUAGE VERSION
        /// , HE IS REDIRECTED THERE AFTER SAVING PRICE LIST FOR BUYER,
        /// SELLER CAN ADD THOSE PRODUCTS INTO EXTENDED PRICE LIST
        if($request->request->has('foreign_products'))
        {
            $foreign_products = Sanitizer::do_strings($request->foreign_products);
            $currency   =   $c_preferred_currency;
        }
       
        $new_products   =   [];
        $adding_new_products = false;
      
        /// IF SELLER IS PRICING PRODUCT LIST FROM BUYER AND HE DOESN'T HAVE SOME
        /// PRODUCTS, HE IS REDIRECTED THERE AFTER SAVING PRICE LIST FOR BUYER,
        /// SELLER CAN ADD THOSE PRODUCTS INTO EXTENDED PRICE LIST
        if(Sanitizer::do_strings([$request->new_products])[0] == 1 && isset($request->new_products))
        {
            $new_products   =   array_keys(   session()->get('new_products')   );
            $language       =   session()->get('language');
            $currency       =   session()->get('currency');
            $department     =   session()->get('department');
            $adding_new_products = true;
            session()->put('adding_new_products',true);
            session()->put('buyers_price_list',true);
        }
        elseif(Sanitizer::do_strings([$request->new_products])[0] != 1 && isset($request->new_products))
        {
            return back();
        }
        
        $available_languages    =   Language::get_language_names($c_all_languages,'key_value')  ;
        $currencies             =   array_combine($c_all_currencies,$c_all_currencies);
        $departments            =   $this->departments->default_departments();
        $new_department         =   true;
       
        if($request->request->count()   ==  0)
        {
            return view('seller.default_prices.create',
                compact('new_department',
                    'company',
                    'departments',
                    'seller_company_id',
                    'currency',
                    'language',
                    'currencies',
                    'available_languages',
                    'adding_new_products'
                   ));
        }
       
        $rate = isset($rates[$c_preferred_currency] [$request->currency])  ?
            $rates[$c_preferred_currency] [$request->currency] : 1;
       
        if(isset($request->rate))
        {
            $rate   =   $request->rate;
            $this->update_conversion_rate( $request,$seller_company_id,$currency,$language,$department);
        }
        
        $saved_rate = 0;
        
        if(!isset(session()->get('prices_in')[$department][$currency]))
        {
            $saved_rate =   $rate;
        }
        
        $default_price_list    =  isset($company->price_lists_extended['price_lists_extended'][$department])
            ? $company->price_lists_extended['price_lists_extended'][$department] : [];
        
        $translations   =   isset( $company->price_lists_extended['price_list_translations'][$department])
            ? $company->price_lists_extended['price_list_translations'][$department] :
        [];
       
       if($default_price_list == [] ) {
           /*CHECK IF PREFERRED LANGUAGE WAS CHANGED TO NEW LANGUAGE
           NOT INCLUDED IN ALREADY USED LANGUAGE, IF TRUE, THERE IS OLD
           PREFERRED LANGUAGE EXTENDED PRICE LIST IN price_list_translations TABLE
           SO WE WILL USE IT AND DISPLAY IT FOR TRANSLATION TO NEW PREFERRED
           LANGUAGE, WE WILL PUT STRINGS IN PLACEHOLDER */
           if (isset($translations[ 'old_price_list' ])) {
        
               $default_price_list = array_values($translations[ 'old_price_list' ])[ 0 ];
               $old_preferred_language = Language::get_language_names(array_keys($translations[ 'old_price_list' ]), 'short_long')[ 'long' ];
               session()->put('old_price_list', true);
           }
       }
       
       
        $is_preferred_language      =  $language  ==   $c_preferred_language  ? true : false ;
        $is_preferred_currency      =  $currency  ==   $c_preferred_currency  ? true : false ;
        $first_time                 =   true;
        $long_language              =   explode('|',Language::get_language_names([$language],'data_in_value')[0])[1]  ;
        $long_preferred_language    =   explode('|',Language::get_language_names([$c_preferred_language],'data_in_value')[0])[1]  ;
        $long_currency              =   Currency::add_data_to_currency([$currency],'string') ;
        $long_preferred_currency    =   Currency::add_data_to_currency([$c_preferred_currency],'string') ;
        $new_products_extended      =   [];
        $translation =  ['needed'    => true, 'to'  =>  $long_language,'from'  =>  $long_preferred_language] ;
        $conversion  =  ['needed'    => true, 'to'  =>  $long_currency,'from'  =>  $long_preferred_currency,
                        'to_short'   =>  $currency,'from_short'  =>  $c_preferred_currency] ;
        
        $is_preferred =   $currency == $c_preferred_currency && $language == $c_preferred_language  ? true:false;
        $sorted_extended = [];
       
       if($default_price_list != [])
       {
           $translation =  ['needed'    => false, 'to'  =>  $long_language,'from'  =>  $long_preferred_language] ;
           $conversion  =  ['needed'    => false, 'to'  =>  $long_currency,'from'  =>  $long_preferred_currency,
                            'to_short'  =>  $currency,'from_short'  =>  $c_preferred_currency] ;
           
           $first_time = false;
           
           if(!isset($translations[$language]) && ($language != $c_preferred_language))
           {
               $translation =  ['needed'    => true, 'to'  =>  $long_language,'from'  =>  $long_preferred_language];
              
           }
           elseif($language != $c_preferred_language)
               {
                   $translations =  $translations[$language];
           }
    
           if(!isset($rates[$c_preferred_currency][$currency]) && ($currency != $c_preferred_currency))
           {
               $conversion  =  ['needed'    => true, 'to'  =>  $long_currency,'from'  =>  $long_preferred_currency,
                                'to_short'  =>  $currency,'from_short'  =>  $c_preferred_currency] ;
           }
         
           foreach($default_price_list as $long_name => $data)
           {
               $sorted_extended[$long_name] =
                   [
                       'product_name' => $data['product_name']  ,
                       'product_code' =>$data['product_code']  ,
                       'price_per_kg' => $data['price_per_kg']*$rate,
                       'stock_level' => $data['stock_level'] ,
                       'low_stock' => $data['low_stock'],
                       'type_brand' => $data['type_brand'],
                       'box_size' => $data['box_size'],
                       'box_price' => $data['box_price'] == '' ? $data['box_price'] : $data['box_price']*$rate,
                       'additional_info' => $data['additional_info'],
                       'unset' => $data['unset']
                   ];
           }
          
           if(isset($new_products) && $new_products != [] )
           {
               foreach($new_products as $new_product)
               {
                   $new_products_extended[$new_product] =
                       [
                           'product_name' =>$new_product  ,
                           'product_code' =>''  ,
                           'price_per_kg' => '',
                           'stock_level' => '' ,
                           'low_stock' => '',
                           'type_brand' => '',
                           'box_size' => '',
                           'box_price' =>'',
                           'additional_info' => '',
                           'unset' => ''
                       ];
               }
               
             //  $sorted_extended =  $new_products_extended;
           }
           if(isset($foreign_products) && $foreign_products != [] )
           {
               $language = $c_preferred_language;
               $adding_new_products = true;
               $is_preferred_language = true;
               $is_preferred_currency = true;
               $is_preferred= true;
               foreach($foreign_products as $new_product)
               {
                   $new_products_extended[$new_product] =
                       [
                           'product_name' =>$new_product  ,
                           'product_code' =>''  ,
                           'price_per_kg' => '',
                           'stock_level' => '' ,
                           'low_stock' => '',
                           'type_brand' => '',
                           'box_size' => '',
                           'box_price' =>'',
                           'additional_info' => '',
                           'unset' => ''
                       ];
               }
              
            //   $sorted_extended =  array_merge($sorted_extended,$new_products_extended);
           }
           
       }
       else
       {
           $new_department = true;
           $language = $c_preferred_language;
       }
     
        $sorted_extended =  array_merge($new_products_extended,$sorted_extended);
       
        $new_products_extended = array_keys($new_products_extended);
        $buyers =   $company->buyer_companies == null ? [] :  $company->buyer_companies;
    
        $view =   $new_products == []  ? 'container':'create' ;
       
        if(isset($foreign_products)) $view ='create';
        if(isset($request->new_additions)) $view ='create';
        
        return view('seller.default_prices.'.$view,
            compact('new_department',
                'new_products_extended',
               
                'company',
                'departments',
                'department',
                'sorted_extended' ,
                'seller_company_id',
                'buyers',
                'currency',
                'language',
                'currencies',
                'c_preferred_currency',
                'c_preferred_language',
                'available_languages',
                'conversion',
                'translation',
                'first_time',
                'is_preferred',
                'is_preferred_language',
                'is_preferred_currency',
                'translations',
                'rate',
                'saved_rate',
                'adding_new_products',
                'adding_foreign_products',
                'old_preferred_language'));
    }
    public function store(Request $request)
    {
       
        $request_data = $this->product_informations($request->request->all());
       
        $product_information = $request_data['product_information'];
        $department = $request_data['department'];
       
        if(empty($product_information))
        {
            return back();
        }
        
        $seller_company_id = session()->get('company_id');
    
        $extended_price_list_hash = $this->extended_price_list($product_information,1)['extended_price_list'];
        
      
        DB::table('price_lists_extended')
            ->insert([
                'seller_id'=> \Auth::guard('seller')->user()->id,
                'seller_company_id'=> $seller_company_id,
                'department' => $department ,
                'price_list' => json_encode($extended_price_list_hash),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s') ]) ;
    
        /// UPDATE COMPANY IN SESSION
        $this->company->update('price_lists_extended');
        
        $subject = __('seller_name has created department prices.',
            [
                'seller_name'  =>   \Auth::guard('seller')->user()->name,
                'department'   =>    $department,
            ]);
        /*NOTIFICATION FOR LOGGED IN SELLERS*/
        $this->notification_for_sellers($department,$seller_company_id,$subject);
        return back();
    }
    public function update_translations(Request $request  )
    {
      
        $request_data = $this->product_informations($request->request->all());
       
        $product_information     =   $request_data['product_information'];
        $department              =   $request_data['department'];
        $language                =   $request_data['language'];
        $seller_company_id       =   session()->get('company_id');
        $product_translations    =   $request_data['translations'];
       
        $current_translations   =  !isset( $this->companies()[$seller_company_id]->price_lists_extended['price_list_translations'][$department])
            ?   []  :   $this->companies()[$seller_company_id]->price_lists_extended['price_list_translations'][$department];
        
        $new_translations      =    $this->extended_price_list($product_information,1,$language,$product_translations)['translations'];
      
        $translations = array_merge($current_translations,$new_translations);
        
        DB::table('price_list_translations')
            ->updateOrInsert([
                'seller_company_id' =>  $seller_company_id,
                'department'        =>  $department,
                'seller_id'        =>  \Auth::guard('seller')->user()->id,
                'deleted_at'        =>  null
            ],
                ['translations'     => json_encode($translations) ,
                  'updated_at'      =>  date('Y-m-d H:m:s')]);
        
        /// UPDATE COMPANY IN SESSION
        $this->company->update(['price_lists_extended']);
    
        /*NOTIFICATION FOR LOGGED IN SELLERS*/
        $this->notification_for_sellers($department,$seller_company_id);
        
        return back();
    }
    public function update(Request $request)
    {
        
        $request_data = $this->product_informations($request->request->all());
     
        $product_information = $request_data['product_information'];
        $department = $request_data['department'];
        $currency =  $request_data['currency'];
        $language =  $request_data['language'];
        
        if(empty($product_information))
        {
            return back();
        }
       
		$seller_company_id = session()->get('company_id');
        
        $pl_translations   =  ! $this->companies()[$seller_company_id]->price_lists_extended['price_list_translations'][$department]
            ?   []  :   $this->companies()[$seller_company_id]->price_lists_extended['price_list_translations'][$department];
       
        $default_price_list = $this->companies()[$seller_company_id]->price_lists_extended['price_lists_extended'][$department];
        
        /*ADDING NEW PRODUCTS AFTER PRICING  BUYERS PRICE LIST IN FOREIGN LANGUAGE*/
        if(isset($request->translations))
        {
         
            /*ADDING NEW PRODUCTS AFTER PRICING  BUYERS PRICE LIST IN FOREIGN LANGUAGE*/
            $additions = $this->extended_price_list($product_information,1,$language,$request->translations);
           
        
            /*ADD EXTRA PRODUCTS TO EXTENDED PRICE LIST
            AND EXTRA TRANSLATIONS TO TRANSLATIONS*/
            $extended_price_list_addition   =   $additions['extended_price_list'];
            $translations_addition   =   $additions['translations'];
            $translations_addition_empty    =   [];
            
            /*ADDING NEWLY ADDED PRODUCTS TO TRANSLATIONS*/
            foreach($translations_addition[$language] as $hash_name =>  $translated)
            {
                $translations_addition_empty[$hash_name]['product_name']    =   null;
                $translations_addition_empty[$hash_name]['type_brand']    =   null;
                $translations_addition_empty[$hash_name]['additional_info']    =   null;
                
            }
          
           foreach($pl_translations as $t_language =>  $translated)
           {
               /*FULL TRANSLATIONS INTO CURRENT LANGUAGE TRANSLATIONS*/
               if($t_language == $language)
    
                   $pl_translations[$t_language]    =    array_merge($pl_translations[$language],$translations_addition[$language]);
               /*EMPTY TRANSLATIONS INTO THE REST OF LANGUAGE TRANSLATIONS*/
               else
                   $pl_translations[$t_language]    =    array_merge($pl_translations[$language],$translations_addition_empty);
           }
           
            $extended_price_list_hash   =   array_merge($default_price_list,$extended_price_list_addition);
    
           // dd($pl_translations,$extended_price_list_hash);
         
            DB::table('price_list_translations')
                ->where('department',$department)
                ->where('seller_company_id',$seller_company_id)
                ->where('deleted_at',null)
                ->update([
                    'translations'   =>  json_encode($pl_translations),
                    'updated_at'     =>  date('Y-m-d H:m:s')]);
    
            DB::table('price_lists_extended')
                ->where([
                    'department' => $department,
                    'seller_company_id' => $seller_company_id,
                    'deleted_at'=>null
                ])
                ->update([
                    'price_list' => json_encode($extended_price_list_hash),
                    'updated_by_seller_id' =>  \Auth::guard('seller')->user()->id,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
    
            /// UPDATE COMPANY IN SESSION
            $this->company->update(['price_lists_extended']);
    
            /*NOTIFICATION FOR LOGGED IN SELLERS*/
            $this->notification_for_sellers($department,$seller_company_id);
            
            
            /*IF SELLER IS PRICING BUYER, AND HE NEEDED TO ADD SOME NEW PRODUCTS,
            BECAUSE BUYER HAS SOME NEW PRODUCTS, WE WILL REDIRECT BACK TO
            BUYER AFTER ADDITION OF NEW PRODUCTS*/
            if(session()->has('buyers_price_list'))
            {
                session()->pull('buyers_price_list');
                session()->pull('adding_new_products');
                
                return redirect('/apply_default_prices');
            }
   
            return redirect('prices/');
        }
      /*UPDATING EXTENDED PRICE LIST IN PREFERRED LANGUAGE*/
        else
        {
            $extended_price_list_data       =   $this->extended_price_list($product_information,1);
            $extended_price_list_addition   =   $extended_price_list_data['extended_price_list'];
            $epl_hash_translations          =   $extended_price_list_data['hash_translations'];
            $default_price_list             =   $default_price_list == null ? [] : $default_price_list;
            $extended_price_list_hash       =   array_merge($default_price_list,$extended_price_list_addition);
            /*FOR FINDING OUT IF CHANGE WAS IN EXISTING PRODUCT OR THERE WAS NEW ADDITION*/
           $extended_price_list_hash_ed   =   array_merge($extended_price_list_addition,$default_price_list);
        }
         if($epl_hash_translations != [])
         {
             /*change hash names in price lists and update it*/
             foreach($epl_hash_translations as $new => $old )
             {
                 unset($extended_price_list_hash[$old]);
             }
         }
      
        $NEW=[] ;
        $EDITED=[];
        $hash_names_changes=[];
        $STRING_CHANGES=[];
        $CHANGED_PRODUCTS=[];
        /*find out when to use which */
        $new_xtra_prods =   array_diff(array_keys($extended_price_list_hash_ed),array_keys($default_price_list));
      
        if($new_xtra_prods != [])
        {
           
            foreach($new_xtra_prods as $prod_index => $hash_name)
            {
                //// THERE WAS CHANGE IN PRODUCT DATA
                if($prod_index < sizeof($default_price_list))
                {
                   
                    $EDITED[$hash_name] =array_slice($default_price_list, $prod_index, 1, true);
                    ///// NEW ->OLD
                    $old_name = array_key_first(array_slice($default_price_list, $prod_index, 1, true));
                  
                    $hash_names_changes[$hash_name] = $old_name;
                   
                    $CHANGED_PRODUCTS[$hash_name] = $extended_price_list_hash[$hash_name];
                  
                    if($CHANGED_PRODUCTS[$hash_name]['product_name'] != $default_price_list[$hash_names_changes[$hash_name]]['product_name'])
                    {
                        $STRING_CHANGES[$hash_name] ['product_name']   =   1;
                    }
                    if($CHANGED_PRODUCTS[$hash_name]['type_brand'] != $default_price_list[$hash_names_changes[$hash_name]]['type_brand'])
                    {
                        $STRING_CHANGES[$hash_name] ['type_brand']   =   1;
                    }
                    if($CHANGED_PRODUCTS[$hash_name]['additional_info'] != $default_price_list[$hash_names_changes[$hash_name]]['additional_info'])
                    {
                        $STRING_CHANGES[$hash_name] ['additional_info']   =   1;
                    }
                  
                    unset($CHANGED_PRODUCTS[$hash_name]['stock_level']);
                    unset($CHANGED_PRODUCTS[$hash_name]['low_stock']);
                }
                /////THERE WAS NEW PRODUCT
                else
                {
                   $NEW[]   =    $hash_name;
                }
               
            }
        }
    
        $buyer_ids  = [0] ;
        if(session()->has('no_buyer_update'))
        {
            /*SELLER IS ADDING PRODUCTS THAT DON'T EXIST YET =>
            NO NEED TO UPDATE BUYERS PRICE LISTS */
            
            session()->pull('no_buyer_update');
        }
        else
        {
            ///////  UPDATING BUYERS PRICE LISTS ONLY
            /// IF SELLER IS UPDATING EXTENDED PRICE LIST,
   $active_price_lists = \App\Services\PriceList::active_price_lists($this->companies()[$seller_company_id],$department);
  
   
   // dd($active_price_lists,$this->companies()[$seller_company_id]);
            if($hash_names_changes != [] && $pl_translations!=null) //// if there was a change and we have translations
            {
                foreach($hash_names_changes as  $new => $old)
                {
                    foreach($pl_translations as $language_tr   =>  $products)
                    {
                        if(isset($pl_translations[$language_tr][$old]))
                        {
                            $pl_translations[$language_tr][$new]   =   $pl_translations[$language_tr][$old];
                            unset($pl_translations[$language_tr][$old]);
                        }
                    }
                }
                
                DB::table('price_list_translations')
                    ->where('department',$department)
                    ->where('seller_company_id',$seller_company_id)
                    ->where('deleted_at',null)
                    ->update([
                        'translations'   =>  json_encode($pl_translations),
                        'updated_at'     =>  date('Y-m-d H:m:s')
                    ]);
            }
    
            if($hash_names_changes     !=  [])
            {
                foreach($active_price_lists as $pl_id=>$pl_data)
                {
                    $price_list_ls_to_update[$pl_id]  = $pl_data['price_list'];
                    $rates[$pl_id]  = $pl_data['rates'];
                    $to_currency[$pl_id] = $pl_data['currency'];
                    $pl_language[$pl_id] = $pl_data['language'];
                  
                    foreach($CHANGED_PRODUCTS as $hash_name=>$details)
                    {
                      
                        /*IF PRICE LIST CONTAINS CHANGED PRODUCT UPDATE IT*/
                        if(isset($price_list_ls_to_update[$pl_id][$hash_names_changes[$hash_name]]))
                        {
                            $buyer_ids[]           =   $pl_data['bc_id'];
                            $price_list_ls_to_update[$pl_id][$hash_name] =   $details ;
                    
                            $price_list_ls_to_update[$pl_id][$hash_name]['product_name'] =
                        
                                isset($pl_translations[$pl_language[$pl_id]][$hash_name]['product_name'])  ?
                                    $pl_translations[$pl_language[$pl_id]][$hash_name]['product_name'] :
                                    $details['product_name']   ;
                    
                            isset($pl_translations[$pl_language[$pl_id]][$hash_name]['type_brand'])  ?
                                $pl_translations[$pl_language[$pl_id]][$hash_name]['type_brand'] :
                                ''   ;
                    
                            isset($pl_translations[$pl_language[$pl_id]][$hash_name]['additional_info'])  ?
                                $pl_translations[$pl_language[$pl_id]][$hash_name]['additional_info'] :
                                ''   ;
                    
                    
                            /*IF CURRENCIES ARE THE SAME FROM AND TO => RATE = 1*/
                            if($currency === $to_currency[$pl_id] )
                            {
                                $price_list_ls_to_update[$pl_id][$hash_name]['price_per_kg'] =
                                    $details['price_per_kg']  ;
                            }
                            else
                            {
                                $price_list_ls_to_update[$pl_id][$hash_name]['price_per_kg'] =
                                    $details['price_per_kg'] * $rates[$pl_id][$currency][ $to_currency[$pl_id]] ;
                            }
                          
                            unset($price_list_ls_to_update [$pl_id][$hash_names_changes[$hash_name]]);
                           
                        }
                    }
                }
        
                if( !empty($price_list_ls_to_update ) )
                {
            
                    foreach($price_list_ls_to_update as $id=>$price_list)
                    {
                        $new_price_lists_to_update[] = [
                            'id'  => $id ,
                            'price_list' =>  json_encode($price_list)
                        ];
                    }
                    /* YOU CAN SEND PUSHER NOTIFICATIONS TO BUYERS ABOUT CHANGE IN PRICE HERE ....
                    IF NECESSARY  $buyers HOLDS ID'S OF BUYER_COMPANIES* /
                    /* dd($buyers);*/
    
                   // dd($request_data,$extended_price_list_data,$new_price_lists_to_update);
                    $this->batch->update(new PriceList, $new_price_lists_to_update, 'id');
                }
            }
            /////// END OF UPDATING BUYERS PRICE LISTS
        }
    
    DB::table('price_lists_extended')
        ->where([
                'department' => $department,
                'seller_company_id' => $seller_company_id,
                'deleted_at'=>null
                ])
        ->update([
                'price_list' => json_encode($extended_price_list_hash),
                'updated_by_seller_id' =>  \Auth::guard('seller')->user()->id,
                'updated_at' => date('Y-m-d H:i:s')
                ]);
        
        $details    =   [
            'n_link'                =>    '/department/'.$department,
            'department'            =>    $department,
            'buyer_ids'             =>    $buyer_ids,
            'action'                =>    'price_list_extended_updated',
            'seller_company_name'   =>    $this->companies()[ $seller_company_id ]->seller_company_name,
            'seller_company_id'     =>    $seller_company_id,
            'subject'               =>   __('seller_company has updated prices.',
                [
                    'seller_company'  =>   $this->companies()[ $seller_company_id ]->seller_company_name
                ]),
    
    
        ];
       
        ///// PUSHER
        BuyerNotificationEvent::dispatch($details);
    
   
        
        /*NOTIFICATION FOR LOGGED IN SELLERS*/
        $this->notification_for_sellers($department,$seller_company_id);
        
        $this->company->update(['price_lists_extended','price_lists']);
        // dd('tu');
        
      if(session()->has('buyers_price_list'))
      {
          session()->pull('buyers_price_list');
          session()->pull('adding_new_products');
          return redirect('/apply_default_prices');
      }
   
        return redirect()->action(
            'Seller\ExtendedPriceListController@create_extended_price_list',
            [   'language' => session()->get('sc_preferred_language'),
                'department' => $department,
                'currency' => session()->get('sc_preferred_currency'),
                'new_additions' => true]
       
        );
        
        
    }
    public function delete_product(Request $request)
    {
        $bc_ids = [];
        if($request->product_name != " ")
        {
            /* SET PRICE TO 0 ON PRODUCT IN ALL SELLER_PRICE_LISTS AS WELL,
            BECAUSE OTHERWISE THEY WILL BE ABLE TO ORDER IT, WITHOUT YOU HAVING IT*/
            

            $active_price_lists = \App\Services\PriceList::active_price_lists(
                $this->companies()[$request->seller_company_id],
                $request->department,
                $request->product_name);
          
          
          if(!empty($active_price_lists ))
         
          {
              $pl_array_old = [];
    
              foreach($active_price_lists as $pl_id => $data)
              {
                  $bc_ids[] =   $data['bc_id'];
                  $pl_array_old[$pl_id] = $data['price_list'];
              }
    
              foreach($pl_array_old as $id => $price_list)
              {
                  $price_list[$request->product_name]['price_per_kg'] = 0;
                  $new_price_lists_to_update[] = [
                      'id'  => $id ,
                      'price_list' =>  json_encode($price_list)
                  ];
              }
          
              $this->batch->update(new PriceList, $new_price_lists_to_update, 'id');
    
              //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
              $details=[
                  'action'                    =>   'product_deleted',
                  'buyer_ids'                 =>    $bc_ids == [] ? [0] : $bc_ids,
              ];
              /////    PUSHER  //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
              BuyerNotificationEvent::dispatch($details);
    
          }
          
          
          /*UNSETING PRODUCT FROM EXTENDED PRICE LIST*/
            $price_list =$this->companies()[$request->seller_company_id]->price_lists_extended['price_lists_extended'][$request->department];
            unset($price_list[$request->product_name]);
            
            $translations = isset($this->companies()[$request->seller_company_id]->price_lists_extended['price_list_translations'][$request->department])
            ? $this->companies()[$request->seller_company_id]->price_lists_extended['price_list_translations'][$request->department]: null;
           
            /*UNSETING PRODUCT FROM TRANSLATED PRICE LISTS*/
            if($translations)
            {
                foreach($translations as $language  =>  $translated_price_list)
                {
                    unset($translated_price_list[$request->product_name]);
                    $translations[$language] =  $translated_price_list;
                }
                
                DB::table('price_list_translations')
                    ->where( 'department',  $request->department)
                    ->where( 'seller_company_id', $request->seller_company_id)
                    ->where('deleted_at',null)
                    ->update([ 'translations' => json_encode($translations),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s') ]);
            }
    
           
            /*NOTIFICATION FOR LOGGED IN SELLERS*/
            $this->notification_for_sellers($request->department,$request->seller_company_id);
            
            ////// last product
            if(empty($price_list))
            {
                
                DB::table('price_lists_extended')
                    ->where( 'department',  $request->department)
                    ->where( 'seller_company_id', $request->seller_company_id)
                    ->update([
                        'deleted_at' => date('Y-m-d H:i:s') ]);
                
                $this->company->update(['price_lists_extended']);
                
                return back();
            }
            ///////// not last product
            else
            {
                
                DB::table('price_lists_extended')
                    ->where( 'department',  $request->department)
                    ->where( 'seller_company_id', $request->seller_company_id)
                    ->where( 'deleted_at', null)
                    ->update([
                        'price_list' => json_encode($price_list),
                        'updated_at' => date('Y-m-d H:i:s') ]);
                
                $this->company->update(['price_lists_extended']);
                
                return back();
            }
            
            
        }
        else
        {
            return ['status'=>'no product','title'=>__('Error'),'text'=>__('There was no product !')];
        }
        
    }
    public function delete_department(Request $request)
    {

        if($request->ajax() )
        {
            
               DB::table('price_lists_extended')
                   ->where( 'department', $request->department)
                   ->where( 'seller_company_id', $request->seller_company_id)
                   ->update([
                       'deleted_at' =>  date('Y-m-d H:i:s'),
                       'seller_id' =>  Auth::guard('seller')->user()->id
                   ]);
    
            DB::table('price_list_translations')
                ->where( 'department', $request->department)
                ->where( 'seller_company_id', $request->seller_company_id)
                ->update([
                    'deleted_at' =>  date('Y-m-d H:i:s'),
                    'seller_id' =>  Auth::guard('seller')->user()->id
                ]);
    
               DB::table('price_lists')
                   ->where('seller_company_id',$request->seller_company_id)
                   ->where('department','=',$request->department)
                   ->delete();
    
            DB::table('product_list_requests')
                ->where('seller_company_id','=',$request->seller_company_id)
                ->where('department','=',str_replace(' ','_',$request->department))
                ->delete();
    
            DB::table('delivery_locations')
                ->where('seller_company_id','=',$request->seller_company_id)
                ->where('department','=',str_replace(' ','_',$request->department))
                ->delete();
            
            $this->company->update(['price_lists_extended','price_lists','delivery_locations']);
            //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
            $details=[
                'n_link'                    =>   '/department/'. $request->department,
                'action'                    =>   'department_deleted',
                'seller_company_id'         =>  $request->seller_company_id,
                'seller_company_name'       =>  $this->companies()[$request->seller_company_id]->seller_company_name,
                'department'                =>  $request->department,
                'subject'                   =>  __('seller_company deleted deleted_department department.',
                    [
                        'seller_company'    =>  $this->companies()[$request->seller_company_id]->seller_company_name,
                        'deleted_department'=>  __(str_replace('_',' ',$request->department))
                    ]),
            ];
            /////    PUSHER  //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
            BuyerNotificationEvent::dispatch($details);
           
            $subject    =   __('seller_name has deleted department prices.',
                [
                    'seller_name'  =>   \Auth::guard('seller')->user()->name,
                    'department'   =>    $request->department,
                ]);
            /*NOTIFICATION FOR LOGGED IN SELLERS*/
            $this->notification_for_sellers($request->department,$request->seller_company_id,$subject);
            
               return ['status'=>'deleted','department'=>str_replace('_',' ',$request->department),'text'=>$request->department.__(' was deleted !')];
           }
          
      
        else{
            abort(401,['not allowed']);
        }

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
    private function notification_for_sellers($department,$seller_company_id,$subject = null)
    {
        if(!$subject)
            $subject   =   __('seller_name has updated department prices.',
                [
                    'seller_name'  =>   \Auth::guard('seller')->user()->name,
                    'department'   =>    $department,
                ]);
        $details    =   [
            'n_link'                =>    '/prices?department='.$department.'&new_additions=1',
            'department'            =>    $department,
            'seller_ids'            =>    $this->seller_ids($seller_company_id),
            'action'                =>    'price_list_extended_updated_for_sellers',
            'seller_name'           =>    \Auth::guard('seller')->user()->name,
            'subject'               =>     $subject,
        ];
    
        SellerNotificationEvent::dispatch($details);
    }
    private function update_conversion_rate(Request $request,$seller_company_id,$currency,$language,$department)
    {
        
        $validator = Validator::make($request->all(), [
            'rate' => 'numeric|required|gt:0',
        ]);
        $errors = $validator->errors();
        
        if ($validator->fails())
        {
            return ['error'=>$errors->first('rate')];
        }
        else {
            $rates = session()->get('rates');
    
            $old_rate = isset($rates[ session()->get('c_preferred_currency') ][ $request->currency ]) ?
                $rates[ session()->get('c_preferred_currency') ][ $request->currency ]
                : 1;
    
    
            $rates[ session()->get('c_preferred_currency') ][ $request->currency ] = $request->rate;
    
            session()->put('rates', $rates);
    
            DB::table('currency_conversion_rates')
                ->updateOrInsert(
                    [
                        'seller_company_id' => $seller_company_id,
                    ],
                    [ 'rates' => json_encode($rates),
                        'updated_at' => date('Y-m-d H:m:s')
                    ]);
    
            /*UPDATE BUYERS PRICE LISTS WHEN CHANGING CONVERSION RATE*/

    
            $active_price_lists = \App\Services\PriceList::active_price_lists_with_currency(
                $this->companies()[ $seller_company_id ],
                $request->currency);
    
            if (!empty($active_price_lists)) {
                foreach ($active_price_lists as $id => $price_list) {
                    foreach ($price_list as $hash_name => $data) {
                        
                        $active_price_lists[ $id ] [ $hash_name ][ 'price_per_kg' ]
                            =    $data[ 'price_per_kg' ]   / $old_rate  * $request->rate ;
    
                        $active_price_lists[ $id ] [ $hash_name ][ 'box_price' ]
                            =    $data[ 'box_price' ]   / $old_rate  * $request->rate ;
                    }
            
                    $new_active_price_lists[] = [
                        'id' => $id,
                        'price_list' => json_encode($active_price_lists[ $id ])
                    ];
                }
                $this->batch->update(new PriceList, $new_active_price_lists, 'id');
            }
        }
        $details    =   [
            'n_link'                =>    '/department/'.$department,
            'department'            =>   $department,
            'currency'              =>   $request->currency,
            'action'                =>   'price_list_extended_updated',
            'seller_company_name'   =>   $this->companies()[ $seller_company_id ]->seller_company_name,
            'seller_company_id'     =>  $seller_company_id,
            'subject'               =>   __('seller_company has updated prices.',
                [
                    'seller_company'  =>   $this->companies()[ $seller_company_id ]->seller_company_name
                ]),
          
    
        ];
        
        ///// PUSHER
        BuyerNotificationEvent::dispatch($details);
        
        $this->company->update(['price_lists_extended','price_lists']);
    }
    private function product_informations($product_information)
    {
        
    
        ////////////// REMOVING _token
        array_shift($product_information);
    
        ///////////////// GETTING DEPARTMENT NAME
        $department = str_replace(' ','_',$product_information['department']);
        unset($product_information['department']);
      
        ///////////////// GETTING currency
        $currency =  $product_information['currency'];
        unset($product_information['currency']);
        ///////////////// GETTING LANGUAGE
        $language =  $product_information['language'];
        unset($product_information['language']);
    
        ///////////////// GETTING TRANSLATIONS
        if(isset($product_information['translations']))
        {
            $translations = $product_information['translations'];
            unset($product_information['translations']);
        }
        else{
            $translations=[];
        }
        ///////////////// REMOVING submit button
        array_pop($product_information);
       
        return [
            'department'            =>  $department,
            'currency'              =>  $currency,
            'language'              =>  $language,
            'translations'          =>  $translations,
            'product_information'   =>  $product_information
             ];
    }
    private function extended_price_list($product_information,$rate,$language=null,$translations=null)
    {
    
  
      if(ArrayIs::multi($product_information))
      {
          $product_information = array_values($product_information)[0];
      }
    
  
        foreach($product_information as $desc =>$value)
        {
            if($desc    != 'converter')
            $products[  explode('|',$desc)[0]  ] [explode('|',$desc)[1]] = $value;
        }
        if(isset($translations))
        foreach($translations as $desc =>$value)
        {
    
            $translations_array[  explode('|',$desc)[0]  ] [explode('|',$desc)[1]] = $value;
        }
   
        $translations_a = [];
        $hash_translations = [];
    
        foreach ($products as $product => $product_data) {
    
            /*CHECK IF PREFERRED LANGUAGE WAS CHANGED TO NEW LANGUAGE
		  NOT INCLUDED IN ALREADY USED LANGUAGE, IF TRUE, WE WILL KEEP
            SAME HASH NAME FOR THE PRODUCT TO BE CONSISTENT WITH TRANSLATIONS AND
            BUYERS PRICE LISTS*/
            if(session()->has('old_price_list'))
            {
                $product_hash_name  = $product_data['old_hash_name'];
            }
            else
            {
                $product_hash_name  =   HashMaker::product($product_data['product_name'],$product_data,$rate);
    
                if($product_hash_name != $product_data['old_hash_name'])
    
                {
                    $hash_translations[$product_hash_name ]= $product_data['old_hash_name'];
                }
            }
            
            
            $extended_price_list_hash[$product_hash_name] = [
                'product_name' => $product_data['product_name'],
                'product_code' => $product_data['product_code'],
                'price_per_kg' =>( $product_data['price_per_kg']+ 0),
                'stock_level'   =>  floatval($product_data['stock_level']) + floatval($product_data['extra_stock']),
                'low_stock' =>  $product_data['low_stock'],
                'type_brand' =>$product_data['type_brand'],
                'box_size' => ($product_data['box_size']+ 0),
                'box_price' => ($product_data['box_price']+ 0),
                'additional_info' => $product_data['additional_info'],
                'unset' => $product_data['unset']
            ];
        
            if(isset($translations) && isset($language))
            {
                if(session()->has('old_price_list'))
                {
                    $product_hash_name  = $product_data['old_hash_name'];
                }
                
                $translation_hash = $product_data[ 'old_hash_name' ] == null ?
                    $product_hash_name :
                    $product_data[ 'old_hash_name' ];
                
                /*KEEPING SAME OLD HASH FOR TRANSLATIONS*/
                $translations_a[$language][$translation_hash]['product_name'] = $translations_array[$product]['product_name'];
                $translations_a[$language][$translation_hash]['type_brand'] = $translations_array[$product]['type_brand'];
                $translations_a[$language][$translation_hash]['additional_info'] =  $translations_array[$product]['additional_info'];
            }
        }
    
       session()->pull('old_price_list');
    
        return [
            'extended_price_list'   =>  $extended_price_list_hash ,
            'translations'          =>  $translations_a,
            'hash_translations'     =>  $hash_translations
        ];
    }
    
}
