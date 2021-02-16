<?php

namespace App\Http\Controllers;

use App\Services\Language;
use Illuminate\Http\Request;
use DB;
use App\Services\Currency;


class LocationsController extends Controller
{
   
    public $currency;
   
    public function __construct(  Currency $currency )
    {
       
        $this->currency     =   $currency;
       
    }
    
    public function display_counties(Request $request)
    {
        //dd(session()->all());
        $country_currency       =       Currency::get_country_currency( $request->selected_country);
        $selected_currency      =       Currency::add_data_to_currency([$country_currency]) ;
        $new_languages          =       Language::new_languages($request->selected_country);
        
        session()->put('selected_country',$request->selected_country);
        session()->put('selected_currency',$country_currency);
        
      
        ///// ONLY EDIT
        if(   session()->has('company_currencies') )
        
        {
            !in_array($country_currency,session()->get('company_currencies')    ) ? : $country_currency=null;
           
        }
       
     
        $all_country_levels = DB::table('test_all_levels')->where('level_name','=','country')->pluck('levels')->first();
     
        if(isset(json_decode($all_country_levels, true)[$request->selected_country]))
        {
            $county_levels = json_decode($all_country_levels, true)[$request->selected_country];
          
            return   [
               'county_names'           =>  array_values($county_levels),
               'county_ids'             =>  array_keys($county_levels),
               'country_currency'       =>  $country_currency,
               'new_languages'          =>  $new_languages,
               'selected_currency'      =>  array_values($selected_currency) [0],
               'creating_company'       =>  session()->get('creating_company'),
           ];
           
        }
        else
        {
            return [
                'end'=>'end',
                'country_currency'       =>  $country_currency
                ];
        }
        
    }
    public function display_counties_4(Request $request)
    {
       
        $all_county_levels = DB::table('test_all_levels')->where('level_name','=','level_4')->pluck('levels')->first();
        
        if(isset(json_decode($all_county_levels, true )[$request->selected_county]))
        {
            $county_levels_4 = json_decode($all_county_levels, true)[$request->selected_county];

            return [/*names*/array_values($county_levels_4),/*ids*/array_keys($county_levels_4)];
        }
        else
        {
            return [
                'end'=>'end'];
        }
        
    }
    
   
}
