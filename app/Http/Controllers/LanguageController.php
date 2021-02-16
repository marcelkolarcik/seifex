<?php

namespace App\Http\Controllers;

use App\Services\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function neighbour_languages(Request $request)
    {
        $country_id = session()->has('selected_country') ?
            session()->get('selected_country')   :   $request->country_id;
        $neighbour_languages =   Language::neighbour_languages( $country_id );
        
        session()->put('who',$request->who);
        session()->put('neighbour_languages',$neighbour_languages);
    
        return  ['who'=>'load_'.$request->who] ;
       
    }
    
    public function load_neighbour_languages()
    {
        
        $neighbour_languages =  session()->get('neighbour_languages');
        
        return view ('includes.forms.neighbour_languages',compact('neighbour_languages'));
        
    }
    
    public function load_remaining_languages(  )
    {
        
        $remaining_languages =   Language::remaining_languages();
        return view ('includes.forms.remaining_languages',compact('remaining_languages'));
    }
}
