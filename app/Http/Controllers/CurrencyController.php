<?php

namespace App\Http\Controllers;

use App\Services\Currency;
use App\Services\Language;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function neighbour_currencies(Request $request)
    {
     
        $country_id = session()->has('selected_country') ?
                session()->get('selected_country')   :   $request->country_id;
        
        session()->put('neighbour_currencies',Currency::neighbour_currencies( $country_id));
        
        return  ['who'=>'load_'.$request->who] ;
        
    }
    
    public function load_neighbour_currencies()
    {
        
        $neighbour_currencies   =   session()->get('neighbour_currencies');
        
        return view ('includes.forms.neighbour_currencies',compact('neighbour_currencies'));
    }
    
    public function load_remaining_currencies()
    {
        $remaining_currencies =   Currency::remaining_currencies();
        
        return view ('includes.forms.remaining_currencies',compact('remaining_currencies'));
    }
    
   
}
