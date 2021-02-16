<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 14-Sep-19
 * Time: 16:45
 */

namespace App\Services;


use App\BuyerCompany;
use App\SellerCompany;
use Illuminate\Support\Facades\DB;


class Currency
{
    static function get_country_currency( $country_id )
    {
       
    
       return json_decode(DB::table('test_all_levels')
            ->where('level_name', 'currencies_by_country_seifex_id')
            ->pluck('levels')
            ->first(), true)[$country_id];
        
        
    }
    function get_seifex_currencies( $plain = null )
    {
        $seifex_currencies_with_country_name = [];
        
        $seifex_currencies = json_decode(DB::table('test_all_levels')
            ->where('level_name', 'currencies')
            ->pluck('levels')
            ->first(), true);
        
        $country_names = json_decode(DB::table('test_all_levels')
            ->where('level_name', 'country_seifex_id_to_country_seifex_name')
            ->pluck('levels')
            ->first(), true);
        
        if ($plain) return $seifex_currencies;
       
        foreach ($seifex_currencies as $currency => $country_ids) {
            
            foreach ($country_ids as $country_id => $value) {
                $seifex_currencies_with_country_name[ $currency ][] = StrReplace::dash($country_names[ $country_id ]);
            }
        }
    
        foreach ( $seifex_currencies_with_country_name as $currency => $countries )
        {
    
            $seifex_currencies_with_country_name[$currency] = implode(',',$countries);
        }
       return $seifex_currencies_with_country_name;
    }
    static function all_currencies()
    {
        return json_decode(DB::table('test_all_levels')
            ->where('level_name','all_currencies')
            ->pluck('levels')
            ->first(),true);
    }
    static function get_company_currencies($type)
    {
        $Company =   \Auth::guard('buyer')->check() ?
            'App\\'.ucwords('buyer').'Company' :
            'App\\'.ucwords('seller').'Company';
      
    
        return json_decode($Company::
        where('id',session()->get('company_id'))
            ->pluck('currencies')
            ->first(),true)
        [$type];
        
    }
    static function get_s_c_all($seller_company_id)
    {
      
        return json_decode(SellerCompany::
        where('id',$seller_company_id)
            ->pluck('currencies')
            ->first(),true)
        ['all'];
        
    }
    static function get_s_c_preferred($seller_company_id)
    {
        return json_decode(SellerCompany::
        where('id',$seller_company_id)
            ->pluck('currencies')
            ->first(),true)
        ['preferred'];
        
    }
    static function get_b_c_all($buyer_company_id)
    {
        
        return json_decode(BuyerCompany::
        where('id',$buyer_company_id)
            ->pluck('currencies')
            ->first(),true)
        ['all'];
        
    }
    static function get_b_c_preferred($buyer_company_id)
    {
        return json_decode(BuyerCompany::
        where('id',$buyer_company_id)
            ->pluck('currencies')
            ->first(),true)
        ['preferred'];
        
    }
    static function neighbour_currencies($country_id)
    {
        
        $neighbour_currencies = json_decode( DB::table('test_all_levels')
            ->where('level_name','neighbour_currencies')
            ->pluck('levels')
            ->first(),true);
        
        $neighbour_currencies =  isset($neighbour_currencies[$country_id]) ? $neighbour_currencies[$country_id] : [];
                            /////// EDIT OR CREATE
        $company_currencies   =   session()->has('company_currencies') ? session()->get('company_currencies') : [] ;
        $country_currency     =   Currency::get_country_currency($country_id);
        
        $visible_currencies   =   array_merge($company_currencies,[$country_currency]);
        
        $neighbour_currencies =  array_diff(  array_merge($neighbour_currencies,Currency::top_currencies()) ,$visible_currencies );
        
        session()->put('visible_currencies_for_remaining',array_merge($visible_currencies,$neighbour_currencies));
     
        return Currency::add_data_to_currency($neighbour_currencies);
        
        
    }
    static function top_currencies()
    {
        return [
                    0  =>  'USD',
                    1  =>  'CAD',
                    2  =>  'EUR',
                    3  =>  'GBP',
                    4  =>  'CHF',
                    5  =>  'NZD',
                    6  =>  'AUD',
                    7  =>  'JPY',
                ];
    }
    static function remaining_currencies()
    {
        
        $remaining_currencies = json_decode( DB::table('test_all_levels')
            ->where('level_name','all_currencies')
            ->pluck('levels')
            ->first(),true);
        
        return Currency::add_data_to_currency( array_diff(array_keys($remaining_currencies),session()->get('visible_currencies_for_remaining')));
        
    }
    public function countries_by_currency( $currency )
    {
        $countries_by_currency     =    json_decode(DB::table('test_all_levels')
            ->where('level_name','countries_by_currency')
            ->pluck('levels')
            ->first(),true)[$currency];
        
        return $countries_by_currency;
    }
    static function add_data_to_currency(array  $currencies,$type = null)
    {
   
        $currencies_with_data   =  Currency::currencies_with_data();
        if($type    ==   'string')
        {
            return    StrReplace::currency_underscore($currencies_with_data[$currencies[0]]);
        }
        foreach($currencies as $key => $currency)
        {
            $return_currencies[$currency] =  $currencies_with_data[$currency] ;
        }
        
        return $return_currencies;
    }
    static function   get_name_for_company($currencies)
    {
     
        $currencies_with_data   =  Currency::currencies_with_data();
        $currencies = is_array($currencies) ? $currencies : json_decode($currencies,true);
      
        if(ArrayIs::multi($currencies))
        {
            foreach($currencies['all'] as $currency)
            {
                $return_currencies['all'] [$currency]   =  StrReplace::currency_underscore($currencies_with_data[$currency])  ;
            }
            $return_currencies['preferred']  =    StrReplace::currency_underscore($currencies_with_data[$currencies['preferred']]);
        }
        else
        {
            foreach($currencies as $currency)
            {
                $return_currencies[]    =  StrReplace::currency_underscore($currencies_with_data[$currency])  ;
            }
        }
       
    
        return $return_currencies;
        
    }
    static function currencies_with_data()
    {
        return  json_decode( DB::table('test_all_levels')
            ->where('level_name','currencies_with_data')
            ->pluck('levels')
            ->first(),true);
    }
}
