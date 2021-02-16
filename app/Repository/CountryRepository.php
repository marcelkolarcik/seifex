<?php

namespace App\Repository;


use DB;


class CountryRepository {
    
    public function current_countries()
    {
       $current_countries   =   DB::table('countries')->where('started_at','!=',null)->pluck('country_name')->toArray();
        
       return  array_combine($current_countries,$current_countries);
    }
    
    
    
    
   
    
}
