<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 30-Sep-19
 * Time: 0:03
 */

namespace App\Services;

class MatchMaker
{
    static function prices_in($s_company)
    {
       
        $prices_in = [];
        if(isset($s_company->price_lists_extended['price_lists_extended']))
        {
            foreach($s_company->price_lists_extended['price_lists_extended'] as $department =>  $price_list)
            {
                $prices_in[$department][$s_company->currencies['preferred']][$s_company->languages['preferred']]    =   1;
            }
        }
        if(isset($s_company->price_lists_extended['price_list_translations']))
        {
            foreach($s_company->price_lists_extended['price_list_translations'] as $department =>  $translations)
            {
                foreach($translations as $language => $translation)
                {
                    if('old_price_list' != $language)
                        $prices_in[$department][$s_company->currencies['preferred']][$language]    =   1;
                }
            }
        }
        if(isset($s_company->price_lists_extended['conversion_rates']))
        {
            foreach($s_company->price_lists_extended['conversion_rates'] as $preferred_currency =>  $other_currencies)
            {
                foreach($other_currencies as $currency  =>  $rate)
                {
                    foreach($prices_in as $_department => $prices_in_d)
                    {
                        $prices_in[$_department][$currency]   =     $prices_in_d[$preferred_currency];
                    }
                }
            }
        }
        
       
        
        return $prices_in;
    }
    static function find_match($buyer_company_id, $department,$buyer_preferred_language,$s_company)
    {
       
        // MATCH IS BASED ON LANGUAGES OF BUYERS PRODUCT LISTS AND CURRENCIES (PREFERRED FIRST)
        /// AND ON CURRENCIES AND LANGUAGES OF SELLERS EXTENDED PRICE LISTS
        ///  BUYER'S DATA ARE PREFERRED
        
        $prices_in = MatchMaker::prices_in($s_company);
        
        $buyer_languages    =    array_keys($s_company->product_lists[$buyer_company_id][$department]);
        $buyer_currencies   =  $s_company->buyer_companies[$buyer_company_id]['currencies'];
       
        if($buyer_currencies == null)
        {
            $buyer_currencies['all'] = [];
            $buyer_currencies['preferred'] = [];
        }
     
        foreach($prices_in[$department] as $currency  => $c_languages)
        {
    
            $seller_currencies[$currency]    =  1;
            foreach($c_languages as $language=>$key)
            {
                $seller_languages[$language]    =   1;
            }
        }
        $seller_currencies  =   array_keys($seller_currencies);
        $seller_languages   =   array_keys($seller_languages);
      
        $language_intersect =   array_intersect($buyer_languages,$seller_languages);
        $currency_intersect =   array_intersect($buyer_currencies['all'],$seller_currencies);
      
    
        $matches['language']  =  ( $buyer_preferred_language   == reset($language_intersect) )?
            $buyer_preferred_language : ( ( reset($language_intersect) == [] )?
                ''  :  reset($language_intersect) );
    
        $matches['currency']  =  ( $buyer_currencies['preferred']   ==  reset($currency_intersect) ) ?
            $buyer_currencies['preferred'] :( ( reset($currency_intersect) == [] )?
                ''  :   reset($currency_intersect));
    
        $matches['match']   =   ($matches['language'] == '' || $matches['currency'] == '') ? false:true;
        
       
        
        $matches['bc_languages']     =   $buyer_languages ;
        $matches['bc_currencies']    =   $buyer_currencies;
        $matches['sc_languages']     =   $seller_languages ;
        $matches['sc_currencies']    =   $seller_currencies;
        
        return $matches;
       
    }
}
