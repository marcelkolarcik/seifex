<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class CountryController extends Controller
{
   public function show($seifex_country_id)
   {
        $country    =   DB::table('countries')->where('seifex_country_id',$seifex_country_id)->first();
        $buyers     =   DB::table('buyer_companies')->where('country',$seifex_country_id)->get();
        $sellers     =   DB::table('seller_companies')->where('country',$seifex_country_id)->get();
        $sales      =   DB::table('seifex_orders')->where('seller_country',$seifex_country_id)->sum('order_value');
        
       
        
        return view('country.show',compact('country','buyers','sellers','sales'));
   }
   function public_countries()
   {
       $countries   =   DB::table('countries')
           ->where('started_at','!=',null)
           ->get(['country_name','continent_name','seifex_country_id'])
           ->groupBy('continent_name')->toArray();
    
        
   }
}
