<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 16-Sep-19
 * Time: 22:58
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;

class Converter
{
    static function get_rate($seller_company_id,$from_currency,$to_currency)
    {
       
        if($from_currency  ==  $to_currency || session()->has('adding_new_products'))
        {
          
            session()->pull('adding_new_products');
            session()->put('no_buyer_update',true);
            return  1;
        }
        
      if  (isset( json_decode( DB::table('currency_conversion_rates')
            ->where('seller_company_id',$seller_company_id)
            ->pluck('rates')
            ->first(),true)[$from_currency][$to_currency]))
      {
          return json_decode( DB::table('currency_conversion_rates')
              ->where('seller_company_id',$seller_company_id)
              ->pluck('rates')
              ->first(),true)[$from_currency][$to_currency];
      }
      else
      {
          return 1;
      }
        
        
      
       
    }
}
