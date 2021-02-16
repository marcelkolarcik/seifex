<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 04-Oct-19
 * Time: 11:48
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;
use App\Services\Company;
use App\Services\Role;

class PriceList
{
    
    static function get($buyers_default_product_list,$matches,$bc_preferred_language,$b_price_list)
    {
       
  
        $new_products = [];
        $language_match = isset($buyers_default_product_list[ $matches[ 'language' ] ]) ? true : false;
        $currency_match = !$matches[ 'currency' ] ? false : true;
       
        if ($language_match && $currency_match)
        {
            
            ///// IF IT IS FIRST TIME PRICING PRODUCTS
            if ($b_price_list == [])
            {
                foreach ($buyers_default_product_list[ $matches[ 'language' ] ] as $product) {

                    $seller_price_list[ $product ][ 'product_name' ] = $product;
                    $seller_price_list[ $product ][ 'product_code' ] = '';
                    $seller_price_list[ $product ][ 'price_per_kg' ] = '';
                    $seller_price_list[ $product ][ 'type_brand' ] = '';
                    $seller_price_list[ $product ][ 'box_size' ] = '';
                    $seller_price_list[ $product ][ 'box_price' ] = '';
                    $seller_price_list[ $product ][ 'additional_info' ] = '';
                    $seller_price_list[ $product ][ 'unset' ] = '';
                }
                $price_list = $seller_price_list;
                $match = true;
            } ///// IF IT IS UPDATING PRODUCTS PRICES, FIND OUT WHICH PRODUCT IS NEW
            else
            {
                $department_price_list = $b_price_list[ 'price_list'];
                $already_priced_products = array_keys($department_price_list, true);
            
                foreach ($already_priced_products as $hash_product) {
                    $plain_priced_products[] = $department_price_list[ $hash_product ][ 'product_name' ];
                }
            
            
                foreach ($buyers_default_product_list[ $matches[ 'language' ] ] as $product) {
                    ///// if there is a new not priced product
                    if (!in_array($product, $plain_priced_products)) {

                        $new_products[ $product ][ 'product_name' ] = $product;
                        $new_products[ $product ][ 'product_code' ] = '';
                        $new_products[ $product ][ 'price_per_kg' ] = '';
                        $new_products[ $product ][ 'type_brand' ] = '';
                        $new_products[ $product ][ 'box_size' ] = '';
                        $new_products[ $product ][ 'box_price' ] = '';
                        $new_products[ $product ][ 'additional_info' ] = '';
                        $new_products[ $product ][ 'unset' ] = '';
                    }
                }
                $price_list = array_merge($new_products, $department_price_list);
                $match = true;
            }
        }
        else/*THERE IS SOME NOT MATCHES*/
        {
           
            foreach ($buyers_default_product_list[ $bc_preferred_language ] as $product) {
                $seller_price_list[ $product ][ 'product_name' ] = $product;
                $seller_price_list[ $product ][ 'product_code' ] = '';
                $seller_price_list[ $product ][ 'price_per_kg' ] = '';
                $seller_price_list[ $product ][ 'type_brand' ] = '';
                $seller_price_list[ $product ][ 'box_size' ] = '';
                $seller_price_list[ $product ][ 'box_price' ] = '';
                $seller_price_list[ $product ][ 'additional_info' ] = '';
                $seller_price_list[ $product ][ 'unset' ] = '';
            }
            $price_list = $seller_price_list;
            $match = false;
        }
        
        $action                           =   '';
        $activated_by_seller              =   0;
        $activated_by_buyer               =   0;
        
        if($b_price_list)
        {
            
            $activated_by_seller              =   $b_price_list['activated_by_seller'];
            $activated_by_buyer               =   $b_price_list['activated_by_buyer'];
           
        }
    
    
        if( $activated_by_seller   ==  1 && $activated_by_buyer ==  1)
        {
            $action  =   'de_activate';
        }
        if($activated_by_buyer ==  0)
        {
            $action  =   'disabled';
        }
        if($activated_by_buyer ==  1)
        {
            $action  =   'de_activate';
        }
        if( $activated_by_seller   ==  0 )
        {
            $action  =   'activate';
        }
    
        session(['action'   => $action]);
        return
            [   'price_list'            =>  $price_list,
                'language_match'        =>  $language_match,
                'currency_match'        =>  $currency_match,
                'match'                 =>  $match,
                'action'                =>  $action
              
                
            ] ;
       
    }
    static function re_sort_price_list($price_list)
    {
        foreach($price_list as $product  =>  $data)
        {
            try{
                $seller_price_list[$product]['product_name']  = $data['product_name'];
                $seller_price_list[$product]['product_code']  = $data['product_code'];
                $seller_price_list[$product]['price_per_kg']  = $data['price_per_kg'];
                $seller_price_list[$product]['type_brand']  = $data['type_brand'];
                $seller_price_list[$product]['box_size']  = $data['box_size'];
                $seller_price_list[$product]['box_price']  = $data['box_price'];
                $seller_price_list[$product]['additional_info']  = $data['additional_info'];
                $seller_price_list[$product]['unset']  = $data['unset'];}
            catch(\Exception $e){
            
                dd('redirect') ;
            }
        }
        
        return $seller_price_list;
    }
  
    
    static function price_list_for_company_department($department,$buyer_company_id,$seller_company_id)
    {
      
       return DB::table('product_list_requests')
           ->leftJoin('price_lists','product_list_requests.buyer_company_id','=','price_lists.buyer_company_id')
            ->where( 'product_list_requests.department', $department)
            ->where( 'product_list_requests.seller_company_id', $seller_company_id)
           ->get(['price_lists.price_list','price_lists.activated_by_buyer','price_lists.activated_by_seller','product_list_requests.delivery_location_id'])
            ->first();
       
     
    }
    static function is_multi($array) {
        return (count($array) != count($array, 1));
    }
    static function find_new_products($product_list,$price_list,$translation)
    {
   
       
        $translated_product_list   =   [];
        $new_products = [];
        $seller_has = [];
        $product_list_use=[];
        
        /*SELLER IS PRICING PRODUCTS IN NOT PREFERRED LANGUAGE*/
        if($translation     !=   [])
        {
           foreach($translation as $hash_name   =>  $translated_data)
           {
               if($translated_data['product_name'] != '' && in_array($translated_data['product_name'],$product_list))
               $translated_product_list[ $hash_name ]   =  $translated_data['product_name'];
           }
           
           
            if($price_list == null)
            {
                foreach($translated_product_list as  $hash_name  =>  $translated_name)
                {
                    $new_products[$translated_name]  = 'disabled'  ;
                }
                return $new_products;
            }
            else
            {
               
                    foreach(array_keys($price_list) as  $hash_name)
                    {
            
                        ///IF SELLER HAS PRICE FOR THE PRODUCT
                        if(in_array($hash_name,array_keys($translated_product_list) ) && $price_list[$hash_name]['price_per_kg'] != 0)
                        {
                            $seller_has[$translated_product_list[$hash_name]]  = 1  ;
                        }
                        /////IF SELLER HAS PRICES FOR ALL OF THE BUYERS PRODUCTS STOP CHECKING OTHER PRODUCTS IN THE PRICE LIST
                        if(sizeof($seller_has) == sizeof(array_values($product_list))) break;
                    }
    
              
                $missing_products    =   array_diff(array_values($product_list),array_keys($seller_has));
              
                if($missing_products  != [])
                {
                    foreach($missing_products as $product)
                    {
                        $new_products[$product]   =   'disabled';
                    }
                }
                
            }
         
            return $new_products;
        }
       
       if(ArrayIs::multi($product_list))
        {
    
            $product_list = array_values($product_list)[0];
        }
      
       if($price_list == null)
       {
           foreach(array_values($product_list) as $key => $name)
           {
               $new_products[$name]  = 'disabled'  ;
           }
           return $new_products;
       }
       else
       {
               foreach(array_keys($price_list) as $key => $hash_name)
               {
                
                 ///IF SELLER HAS PRICE FOR THE PRODUCT
                   if(in_array(explode('+',$hash_name)[0],array_values($product_list) ) && $price_list[$hash_name]['price_per_kg'] != 0)
                   {
                       $seller_has[explode('+',$hash_name)[0]]  = 1  ;
                   }
                   /////IF SELLER HAS PRICES FOR ALL OF THE BUYERS PRODUCTS STOP CHECKING OTHER PRODUCTS IN THE PRICE LIST
                   if(sizeof($seller_has) == sizeof(array_values($product_list))) break;
               }
         
       }
       
       $missing_products    =   array_diff(array_values($product_list),array_keys($seller_has));
       
      if($missing_products  != [])
      {
          foreach($missing_products as $product)
          {
              $new_products[$product]   =   'disabled';
          }
      }
 
       
        return $new_products;
    }
    static function disable_language(array $languages)
    {
      
        if($languages   !=  [])
        {
            foreach($languages as $language)
            {
                DB::table('price_lists')
                    ->where('buyer_company_id',session()->get('company_id'))
                    ->where('language',$language)
                    ->update([
                        'activated_by_buyer'    =>  0,
                        'buyer_disabled_language'  =>  1,
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ]);
            }
        }
        
    }
    static function enable_language(array $languages)
    {
        if($languages   !=  [])
        {
            foreach($languages as $language)
            {
                DB::table('price_lists')
                    ->where('buyer_company_id',session()->get('company_id'))
                    ->where('language',$language)
                    ->update([
                        
                        'buyer_disabled_language'  =>  0,
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ]);
            }
        }
    }
    static function disable_currency(array $currencies)
    {
       
        if($currencies   !=  [])
        {
            foreach($currencies as $currency)
            {
                DB::table('price_lists')
                    ->where('buyer_company_id',session()->get('company_id'))
                    ->where('currency',$currency)
                    ->update([
                        /*AND SET activated_by_buyer == 0*/
                        'activated_by_buyer'    =>  0,
                        'buyer_disabled_currency'  =>  1,
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ]);
            }
        }
        
    }
    static function enable_currency(array $currencies)
    {
        if($currencies   !=  [])
        {
            foreach($currencies as $currency)
            {
                DB::table('price_lists')
                    ->where('buyer_company_id',session()->get('company_id'))
                    ->where('currency',$currency)
                    ->update([
                        'buyer_disabled_currency'  =>  0,
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ]);
            }
        }
    }
    
   static function active_price_lists($company,$department,$product = null)
   {
       $price_lists =   [];
      
      /*NO PRICE LISTS*/
      if($company->price_lists == null)   return $price_lists;
      
       foreach($company->price_lists as $bc_id  =>  $departments)
       {
           foreach($departments as $pl_department  =>  $price_list)
           {
              if($pl_department === $department)
              {
                  if($product && isset($price_list['price_list'][$product]) && $price_list['price_list'][$product]['price_per_kg']  > 0)
                  {
                      $price_lists[$price_list['id']]   =   [
                      'bc_id'            =>  $bc_id,
                      'language'         =>  $price_list['language'],
                      'currency'         =>  $price_list['currency'],
                      'price_list'       =>  $price_list['price_list'],
                      'rates'            =>  $company->price_lists_extended['conversion_rates']
                    ];
                  }
                  elseif(!$product)
                  {
                      $price_lists[$price_list['id']]   =   [
                          'bc_id'            =>  $bc_id,
                          'language'         =>  $price_list['language'],
                          'currency'         =>  $price_list['currency'],
                          'price_list'       =>  $price_list['price_list'],
                          'rates'            =>  $company->price_lists_extended['conversion_rates']
                      ];
                  }
              }
           }
       }
      return $price_lists;
      
   }
    static function active_price_lists_with_currency($company,$currency)
    {
        $price_lists =   [];
     
        /*NO PRICE LISTS*/
        if($company->price_lists == null)   return $price_lists;
        
        foreach($company->price_lists as $bc_id  =>  $departments)
        {
            foreach($departments as $pl_department  =>  $price_list)
            {
                if($price_list['currency'] === $currency)
                {
                    $price_lists[$price_list['id']]   =    $price_list['price_list'];
                   
                }
            }
        }
        return $price_lists;
        
    }
}
