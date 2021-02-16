<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 17-Sep-19
 * Time: 22:55
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;
use App\Services\LocationNameOrId;
use App\Services\Currency;
use Illuminate\Support\Facades\Lang;

class Language
{
    public $currency;
    public function __construct( Currency $currency )
    {
        $this->currency =   $currency;
    }
    public function get_seifex_languages(  )
    {
    
        return  $this->languages_by_country_seifex_id(LocationNameOrId::current_countries_id());
        
   }
    public function available_languages(array $country_ids)
    {
        $languages=[];
        foreach ( $this->languages_by_country_seifex_id($country_ids) as $language=>$item) {
            
            $languages[$language]   =   $language;
        }
        return    $languages;
    }
    static function languages_by_country_seifex_id(array $country_ids)
   {
      
       $languages  =    json_decode(DB::table('test_all_levels')
              ->where('level_name','languages_by_country_seifex_id')
              ->pluck('levels')
              ->first(),true);
      
       $available_languages     =   [];
       $languages_by_country    =   [];
       
       foreach($country_ids as $country_id)
       {
           if(isset($languages[$country_id]))
           {
               $available_languages[] =  $languages[$country_id];
           }
          
       }
     
       if($available_languages)
       {
           $full_lang = call_user_func_array('array_merge', $available_languages);
         
           foreach ($full_lang as $language    =>  $key)
           {
              //$languages_by_country[explode('-',$language)[0]]  =  \Locale::getDisplayLanguage($language, $language);
               $languages_by_country[]  = explode('-',$language)[0];
           }
       }
      // dd($available_languages,$full_lang,$languages_by_country);
    
       return $languages_by_country;
   }
    static function new_languages($country_id)
    {
        $company_languages      =   Language::company_languages();
        $country_languages      = Language::languages_by_country_seifex_id([$country_id]);
        return   Language::get_language_names(array_diff($country_languages,array_intersect($country_languages,$company_languages)),
            true);
    }
    static function country_language( $country_id , $first = null)
    {
        
        if( $first )
        return    explode('-',
                        array_key_first(
                            json_decode(DB::table('test_all_levels')
                                    ->where('level_name','languages_by_country_seifex_id')
                                    ->pluck('levels')
                                    ->first(),
                                true)
                            [$country_id]))
                    [0];
      
       else
       {
         
           return  json_decode(DB::table('test_all_levels')
               ->where('level_name','languages_by_country_seifex_id')
               ->pluck('levels')
               ->first(),
               true)[$country_id];
       }
    }
    public function languages_by_currency( array $country_ids )
    {
        $languages_by_currency
            =    json_decode(DB::table('test_all_levels')
            ->where('level_name','languages_by_currency')
            ->pluck('levels')
            ->first(),true);
        
        $available_currencies =   $this->currency->get_seifex_currencies('plain');
        
        foreach($languages_by_currency as $currency => $language_array )
        {
            if(isset($available_currencies[$currency]))
                $languages[]    =   $language_array;
        }
        $full_lang = call_user_func_array('array_merge', $languages);
        
        return $full_lang;
    }
    public function language_by_currency( array $country_ids, $currency )
    {
    
        return  array_key_first(    $this-> languages_by_country_seifex_id(
                                               array_values( array_intersect(
                                                        $this->countries_by_currency($currency),      $country_ids   )  ) )  );
        
    }
    public function countries_by_currency( $currency )
    {
        $countries_by_currency     =    json_decode(DB::table('test_all_levels')
            ->where('level_name','countries_by_currency')
            ->pluck('levels')
            ->first(),true)[$currency];
        
        return $countries_by_currency;
    }
    static function neighbour_languages( $country_id )
    {
      
       $neighbour_languages = json_decode( DB::table('test_all_levels')
        ->where('level_name','neighbour_languages')
        ->pluck('levels')
        ->first(),true);
    
       $neighbour_languages             =  isset($neighbour_languages[$country_id]) ? $neighbour_languages[$country_id] : [];
                                     /////// EDIT OR CREATE
       $company_languages               =   Language::get_language_names( Language::company_languages() ,'key_value') ;
       $country_languages               =   Language::country_language($country_id);
        
        $visible_languages   =   array_merge($company_languages,$country_languages);
    
    
        ///// ADDING TOP LANGUAGES AS NEIGHBOUR LANGUAGES TO OFFER BIGGER CHOICE
        $neighbour_languages =  array_diff(  array_merge($neighbour_languages,Language::top_languages()) ,$visible_languages );
       
        session()->put('visible_languages_for_remaining',array_merge($visible_languages,$neighbour_languages));
  
        return $neighbour_languages;
       
    }
    static function remaining_languages()
    {
       
        $remaining_languages = json_decode( DB::table('test_all_levels')
            ->where('level_name','all_languages')
            ->pluck('levels')
            ->first(),true);
        
   // dd($remaining_languages, array_diff($remaining_languages,session()->get('visible_languages_for_remaining')) );
        return array_diff($remaining_languages,session()->get('visible_languages_for_remaining')) ;
//
    }
    static function get_language_names(array $languages,$type=null)
    {
        $all_languages  =  json_decode( DB::table('test_all_levels')
            ->where('level_name','all_languages')
            ->pluck('levels')
            ->first(),true);
       
      if($type ==   'data_in_value')
      {
         
          if($languages != [])
          {
              foreach($languages as $key => $language)
              {
                  $return_languages[]   =  $language.'|'. $all_languages[$language];
              }
              
              return  $return_languages;
          }
          
          else  $return_languages = [];
         
      }
      elseif($type  ==  'short_long')
      {
         
          if(sizeof($languages) > 1)
          {
              foreach($languages as $language)
              {
                  $return_languages['short'][]   =   $language;
                  $return_languages['long'] []   =   $all_languages[$language];
              }
          }
          else
          {
         
              foreach($languages as $language)
              {
              
              $return_languages['short']  =   $language;
              $return_languages['long']   =   $all_languages[$language];
              }
          }
          
      }
      elseif($type ==   'key_value')
      {
          if($languages != [])
          {
              foreach($languages as $language)
              {
                  $return_languages[$language]   =   $all_languages[$language];
              }
          }
          else  $return_languages = [];
      }
      elseif($type ==   'iso_in_value')
      {
          if($languages != [])
          {
              foreach($languages as $language)
              {
                  $return_languages[]   =   $language;
              }
          }
          else  $return_languages = [];
      }
      else
      {
          ///// FOR COMPANIES LANGUAGES
          foreach($languages as $type =>  $langs)
          {
             
              if($type == 'all')
              {
                  foreach($langs as $lang)
                  {
                      $return_languages[$type] [$lang]   =   $all_languages[$lang];
                  }
              }
              else{
                  $return_languages[$type] [$langs]   =   $all_languages[$langs];
              }
        
          }
      }
       
       
        return $return_languages;
    }
    static function names_for_company($languages)
    {
        $all_languages  =  json_decode( DB::table('test_all_levels')
            ->where('level_name','all_languages')
            ->pluck('levels')
            ->first(),true);
        $languages = is_array($languages) ? $languages : json_decode($languages,true);
        
     
        if(ArrayIs::multi($languages))
        {
           
            foreach($languages['all'] as $language)
            {
                $return_languages['all'] [$language]   =  $all_languages[$language] ;
            }
            $return_languages['preferred']  =   $all_languages[$languages['preferred']];
        }
        else
        {
            foreach($languages as $language)
            {
                $return_languages[]   = $all_languages[$language] ;
            }
        }
       
        
        return $return_languages;
    }
    static function top_languages(  $plain  =   false)
    {
        if(!$plain)
        return  [
            "de" => "Deutsch",
            "en" => "English",
            "it" => "italiano",
            "fr" => "français",
            "es" => "español",
            "pl" => "polski"
        ];
    
        return  [
            "0" => "de",
            "1" => "en",
            "2" => "it",
            "3" => "fr",
            "4" => "es",
            "5" => "pl"
        ];
    }
    static function company_languages()
    {
        return   session()->has('company_languages') ? session()->get('company_languages') : [] ;
    }
}
