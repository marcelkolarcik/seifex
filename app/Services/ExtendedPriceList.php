<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 21-Oct-19
 * Time: 10:35
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;

class ExtendedPriceList
{
    static function change_preferred_language($old_preferred_language,$preferred_language,$old_all_languages,$seller_id)
    {
        
            $price_lists = DB::table('price_lists_extended')
                ->where('seller_company_id',session()->get('company_id'))
                ->where('deleted_at',null)
                ->pluck('price_list','department')
                ->toArray();
            
            $translations   =   DB::table('price_list_translations')
                ->where('seller_company_id',session()->get('company_id'))
                ->where('deleted_at',null)
                ->pluck('translations','department')
                ->toArray();
            
            $department_translations    =   [];
            $new_price_lists   =   [];
            foreach($translations as $tr_department =>  $dep_translations)
            {
                foreach (json_decode($dep_translations,true) as $language => $dep_translation )
                {
                    $department_translations[$tr_department] = json_decode($dep_translations,true);
                }
            }
           
            foreach($price_lists as $pl_department =>  $price_list)
            {
                $new_price_lists[$pl_department]    = json_decode($price_list,true);
    
                /*IF NEW PREFERRED LANGUAGE IS NOT ONE OF EXISTING LANGUAGES WE WILL SAVE OLD
                PREFERRED LANGUAGE EXTENDED LIST IN TRANSLATIONS TABLE AND WILL DISPLAY IT
                WITH STRINGS IN PLACEHOLDER ONLY*/
                if(json_decode($price_list,true) != null)
                {
                    if(!in_array($preferred_language,$old_all_languages))
                    {
                        $department_translations[$pl_department]['old_price_list'][$old_preferred_language]  =   json_decode($price_list,true);
                    }
                 
                  
                    
                   
                    
                    foreach (json_decode($price_list,true) as $hash_name => $product_details )
                    {
                        $department_translations[$pl_department][$old_preferred_language][$hash_name]['product_name'] =   $product_details['product_name'];
                        $department_translations[$pl_department][$old_preferred_language][$hash_name]['type_brand'] =   $product_details['type_brand'];
                        $department_translations[$pl_department][$old_preferred_language][$hash_name]['additional_info'] =   $product_details['type_brand'];
        
                        /*IF NEW PREFERRED LANGUAGE IS ONE OF EXISTING ACCEPTED LANGUAGES AND IF SELLER HAS TRANSLATION FILE FOR IT
                        WE WILL SET PRICE LIST STRINGS TO NEW PREFERRED LANGUAGE STRINGS(   product_name, type_brand, additional_info)*/
                       
                        if(in_array($preferred_language,$old_all_languages))
                        {
                            $new_price_lists[$pl_department][$hash_name]['product_name']    =
                                isset( $department_translations[$pl_department][$preferred_language][$hash_name]['product_name'] )
                            ? $department_translations[$pl_department][$preferred_language][$hash_name]['product_name'] : '';
        
                            $new_price_lists[$pl_department][$hash_name]['type_brand']    =
                                isset( $department_translations[$pl_department][$preferred_language][$hash_name]['type_brand'] )
                                ? $department_translations[$pl_department][$preferred_language][$hash_name]['type_brand'] : '';
        
                            $new_price_lists[$pl_department][$hash_name]['additional_info']    =
                                isset( $department_translations[$pl_department][$preferred_language][$hash_name]['additional_info'] )
                                ? $department_translations[$pl_department][$preferred_language][$hash_name]['additional_info'] : '';
                        }
                        
                        /*ELSE , IF IT IS NEW LANGUAGE, NOT INCLUDED IN ACCEPTED LANGUAGES, WE WILL SET PRICE LIST
                        TO NULL AND ON FIRST LOAD WE WILL GET OLD PREFERRED LANGUAGE PRICE LIST
                        FROM price_list_translations TABLE....*/
                        
                        else
                        {
                            $new_price_lists[$pl_department]    =  null;
                        }
                    }
                }
            }
            
 
            foreach($department_translations as $department =>   $translation)
            {
               dd($translation);
                DB::table('price_list_translations')
                    ->updateOrInsert(
                        [
                            'seller_company_id'     =>  session()->get('company_id'),
                            'department'            =>  $department,
                            'deleted_at'            =>  null,
                            'updated_at'            =>  date('Y-m-d H:m:s'),
                            'seller_id'             =>  $seller_id
                        ],
                        [   'translations'           =>  json_encode($translation),
                            'created_at'             =>  date('Y-m-d H:m:s')
                        ]);
                
               
    
               
           
            }
        foreach($new_price_lists as $department=>   $price_list)
        {
            
            DB::table('price_lists_extended')
                ->where('seller_company_id',session()->get('company_id'))
                ->where('department',$department)
                ->where('deleted_at',null)
                ->update(['price_list'=>  json_encode($price_list)]);
        
        }
        
            return true;
       
    }
    
    static function change_preferred_currency($old_preferred_currency,$preferred_currency,$old_all_currencies)
    {
        
        $rates   =  json_decode(  DB::table('currency_conversion_rates')
            ->where('seller_company_id',session()->get('company_id'))
            ->pluck('rates')
            ->first(),true)  ;
    
        if(in_array($preferred_currency,$old_all_currencies))
        {
            /*RECALCULATING RATES TO NEW PREFERRED CURRENCY*/
            $converter  =   1 / $rates[$old_preferred_currency][$preferred_currency];
          
            foreach ($rates as $from    => $tos)
            {
                foreach(array_keys($tos) as $to)
                {
                  if($to !== $preferred_currency)
                    $new_rates[$preferred_currency][$to] =   $converter * $rates[$from][$to];
                }
            }
            $new_rates[$preferred_currency][$from] =   $converter ;
    
           DB::table('currency_conversion_rates')
                ->where('seller_company_id',session()->get('company_id'))
                ->update(['rates'   => json_encode($new_rates) ]);
          
        }
        else
        {
            /*NEW PREFERRED CURRENCY, NO CONNECTION TO EXISTING RATES
            DELETE RATES
            OR LEAVE THEM AS IS FOR POSSIBLE FUTURE USE ???*/
        }
       
        
        
        return true;
    }
    /* DISABLING PRICES IN AND PRICE LISTS WHERE LANGUAGE in LANGUAGES*/
    static function disable_language( array $languages = null)
    {
        
        $prices_in   =  json_decode(  DB::table('prices_in')
            ->where('seller_company_id',session()->get('company_id'))
            ->pluck('prices_in')
            ->first(),true)  ;
    
        $prices_in = $prices_in == null ? [] : $prices_in;
        foreach($prices_in as $department =>  $prices)
        {
            foreach($prices as $currency    =>  $price_languages)
            {
               foreach(array_keys($price_languages) as $language)
               {
                   if(in_array($language,$languages))
                   {
                       unset($prices_in[$department][$currency][$language]);
                       
                       if(empty($prices_in[$department][$currency]))
                       {
                           unset($prices_in[ $department ][ $currency ]);
                       }
                   }
               }
            }
        }
        DB::table('prices_in')
            ->where('seller_company_id',session()->get('company_id'))
            ->update(['prices_in'=> json_encode($prices_in) ]);
       
        foreach($languages as $language)
        {
            DB::table('price_lists')
                ->where('seller_company_id',session()->get('company_id'))
                ->where('language',$language)
                ->update([
                    'activated_by_seller'    =>  0,
                    'seller_disabled_currency'  =>  1,
                    'updated_at'    =>  date('Y-m-d H:i:s')
                ]);
        }
    }
    
    /* DISABLING PRICES IN AND PRICE LISTS WHERE CURRENCY in CURRENCIES*/
    static function disable_currency( array $currencies = null)
    {
        $prices_in   =  json_decode(  DB::table('prices_in')
            ->where('seller_company_id',session()->get('company_id'))
            ->pluck('prices_in')
            ->first(),true)  ;
    
        foreach($prices_in as $department =>  $prices)
        {
            foreach($prices as $currency    =>  $price_languages)
            {
                    if(in_array($currency,$currencies))
                    {
                        unset($prices_in[$department][$currency]);
                    
                        if(empty($prices_in[$department]))
                        {
                            unset($prices_in[ $department ]);
                        }
                    }
            }
        }
        
        DB::table('prices_in')
            ->where('seller_company_id',session()->get('company_id'))
            ->update(['prices_in'=>    json_encode($prices_in) ]);
        
        foreach($currencies as $currency)
        {
            DB::table('price_lists')
                ->where('seller_company_id',session()->get('company_id'))
                ->where('currency',$currency)
                ->update([
                    'activated_by_seller'    =>  0,
                    'seller_disabled_currency'  =>  1,
                    'updated_at'    =>  date('Y-m-d H:i:s')
                ]);
        }
    }
}
