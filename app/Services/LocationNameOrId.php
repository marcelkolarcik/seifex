<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 18-Sep-19
 * Time: 9:34
 */

namespace App\Services;


use App\Lists\location_id_to;
use App\Lists\location_name_and_path;
use App\Lists\location_name_to;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


class LocationNameOrId
{
    
    static function get_paths($paths, $delivery_location_delivery_days)
    {
        $locations = location_name_and_path::get();
        
        foreach($paths as $id)
        {
            $new_paths[$id] = $locations[$id]['location_name'].' | '.$locations[$id]['path'];
        }
        
        $return_paths   =   [];
        $dl_index   =   [];
        foreach($delivery_location_delivery_days as $sc_id => $department_paths)
        {
            if($department_paths)
            {
                foreach($department_paths as $department => $s_paths)
                {
                    foreach($s_paths as $delivery_location_id   =>  $path_data)
                    {
            
                        if(!strstr(explode(' | ', $new_paths[ $path_data['path'] ])[1],':'))
                        {
                            $path =[StrReplace::dash( explode(' | ', $new_paths[ $path_data['path'] ])[0]) => ''] ;
                        }
                        else
                        {
                            $long_path =  explode(' : ',explode(' | ', $new_paths[ $path_data['path'] ])[1]) ;
                
                            unset($long_path[0]);
                            $path =[StrReplace::dash( explode(' | ', $new_paths[ $path_data['path'] ])[0] )=>
                                implode(' : ',array_reverse( StrReplace::hash_to_pipe($long_path) ))  ]  ;
                        }
                        $dl_index[$delivery_location_id] = $path;
                        $return_paths[$sc_id][$department][$delivery_location_id] =
                            [
                                'location_id'   =>  $path_data['path'],
                                'path'          =>      $path ,
                                'delivery_days' =>    json_decode($path_data['delivery_days'],true)
                               ];
                        
                    }
                }
            }
            else
            {
                $return_paths[$sc_id]  =   null;
            }
        }
      
        return ['dl_full' => $return_paths, 'dl_index'  =>  $dl_index] ;
    }
    static function path(  $company )
    {
      
        $location_id = '';
       if(is_array($company) )
       {
           if(isset($company[0]) && $company[0])
           {
               if(isset($company[2]) && $company[2] != null) $location_id = $company[2] ;
               elseif(isset($company[1]) && $company[1] != null) $location_id = $company[1] ;
               elseif(isset($company[0]) && $company[0] != null) $location_id = $company[0] ;
           }
           else
           { ////// SEARCH FOR BUYERS
               if(isset($company['county_l4'])  &&  $company['county_l4'] != null) $location_id = $company['county_l4'] ;
               elseif(isset($company['county'])  &&  $company['county'] != null) $location_id = $company['county'] ;
               elseif(isset($company['country'])  &&  $company['country'] != null) $location_id = $company['country'] ;
           }
           
       }
       else{
           if($company->county_l4 != null) $location_id = $company->county_l4 ;
           elseif($company->county != null) $location_id = $company->county ;
           elseif($company->country != null) $location_id = $company->country ;
       }
       
       
        $locations = location_name_and_path::get();
        $full_path = StrReplace::hash_to_pipe($locations[$location_id]['path']).' : '.
            StrReplace::dash($locations[$location_id]['location_name']) ;
        
        return $full_path;
    }
    
    static function get_name_and_path( $ids )
    {
        $locations = location_name_and_path::get();
       
        $location_names  =   [];
    
       foreach($ids as $id )
       {
           $location_names[$id]  =   $locations[$id];
       }
     
      return $location_names;
    }
    
    static function get_names( $ids )
    {
        $names  =   [];
       foreach($ids as $id)
       {
           $names[$id] =    location_id_to::name()[$id];
       }
       return $names;

    }
//    public function get_ids( $names)
//    {
//        $ids  =   [];
//        foreach($names as $name)
//        {
//            $ids[] =    array_flip(location_id_to::name())[$name];
//        }
//        return $ids;
//
//    }
    static function get_name(  $location_id  )
    {
        return StrReplace::hash(location_id_to::name()[$location_id]);
    }
//    public function country_name( $country_seifex_id )
//    {
//        return StrReplace::hash(location_id_to::name()[$country_seifex_id]);
//    }
    static function get_id(  $location_name  )
    {

        return StrReplace::hash(array_flip(location_id_to::name()) [$location_name]);
    }
    
   
    
    static function current_countries_for_select()
    {
        return DB::table('countries')
            ->where('started_at','!=',null)
            ->pluck('country_name','seifex_country_id')
            ->toArray();
        
    }
    static function current_countries_id()
    {
        return DB::table('countries')->where('started_at','!=',null)->pluck('seifex_country_id')->toArray();
    }
    
    static function group_companies($companies)
    {
       
        $ids = [];
        $grouped_companies = [];
        if($companies == []) return [];
        foreach($companies as $key =>$company)
        {
        
            $ids[]=$company->country;
            $ids[]=$company->county;
            $ids[]=$company->county_l4;
        }
    
        $names  =   LocationNameOrId::get_names($ids);
    
    
        foreach($companies as $key =>$company)
        {
           
            $company->path =StrReplace::hash_to_pipe($names[$company->country]) ;
            isset($names[$company->county]) ?  $company->path .= ' , '.StrReplace::hash_to_pipe($names[$company->county]): '' ;
            isset($names[$company->county_l4]) ? $company->path .= ' , '.StrReplace::hash_to_pipe($names[$company->county_l4]): '' ;
            
           
           if($company->department != '')
            $grouped_companies[$company->department][$company->path] [$company->name]=   $company;
        
        }
      
        return $grouped_companies;
    }
    
   
}
