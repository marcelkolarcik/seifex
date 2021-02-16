<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Repository\cRepository;
use App\Http\Controllers\Controller;
use DB;

class AllLevelsController extends Controller
{
    public $repository;



    public function __construct(cRepository $repository)
    {
        $this->repository = $repository;
    }

    public function  showId()
      {
          $location_to_id = $this->repository->location_to_id();

      }

    public function get_all_in_one()
    {
        $all_levels = DB::table('test_all_levels')->get();

     //  dd($all_levels);
        $countries_levels = DB::table('test_all_levels')->where('level_name','=','country')->pluck('levels')->first();
     // dd(array_keys(json_decode($countries_levels, true)));
        $all_levels_decoded = [];
        foreach($all_levels as $level)
        {
            $all_levels_decoded[$level->level_name] = json_decode($level->levels, true);
        }
    // dd($all_levels_decoded);


        $specific = DB::table('test_all_levels')->where('level_name','=','Slovensko--Slovakia')->pluck('levels')->first();
     // dd(json_decode($specific, true));


        $countries_by_continent = [];
        $countries_with_counties_by_continent = [];
        $countries_with_counties_with_l4__by_continent = [];
        $countries_with_counties_with_l5__by_continent = [];
        $countries_with_counties_with_l6__by_continent = [];
        $e =0;
        foreach($all_levels_decoded['continent'] as $continent_name => $continent_countries)
        {
            $e ++;
            foreach ($continent_countries as $key => $country_name)
            {
                $e ++;
                $countries_by_continent[ $continent_name ][ $country_name ] = $country_name;
            }
        }

                /// TO BE CONTINUED HERE counties
                foreach($countries_by_continent as $continent2 => $countries2)
                {
                    $e ++;
                    foreach ($countries2 as $country_name2 => $country_name_value2)
                    {

                        $e ++;
                        if (isset($all_levels_decoded['country'][ $country_name2 ]))
                        {
                            $countries_with_counties_by_continent[ $continent2 ][ $country_name2 ] = $all_levels_decoded['country'][ $country_name2 ];
                        } else // COUNTRY DOESN'T HAVE COUNTIES
                        {
                            $countries_with_counties_by_continent[ $continent2 ][ $country_name2 ] = [];
                        }
                    }
                }
                ////all good here

                        /// TO BE CONTINUED HERE level_4
                        foreach($countries_with_counties_by_continent as $continent3 => $countries3)
                        {
                            $e ++;
                            foreach ($countries3 as $country_name3 => $counties3)
                            {
                                $e ++;
                                // COUNTRY DOESN'T HAVE COUNTIES
                                if (!isset($all_levels_decoded['country'][ $country_name3 ]))
                                {
                                    $countries_with_counties_with_l4__by_continent[ $continent3 ][ $country_name3 ] = [];
                                }
                                foreach ($counties3 as $key => $county_name3)
                                {
                                    $e ++;

                                    if (isset($all_levels_decoded['level_4'][ $county_name3 ]))
                                    {
                                        $countries_with_counties_with_l4__by_continent[ $continent3 ][ $country_name3 ][ $county_name3 ] = $all_levels_decoded['level_4'][ $county_name3 ];
                                    } else
                                    {
                                        $countries_with_counties_with_l4__by_continent[ $continent3 ][ $country_name3 ][ $county_name3 ] = [];
                                    }
                                }
                            }
                        }
                         //// all good here

                                    /// TO BE CONTINUED HERE level_5
                                    foreach($countries_with_counties_with_l4__by_continent as $continent4 => $countries4)
                                    {
                                        $e ++;

                                        foreach ($countries4 as $country_name4 => $counties4)
                                        {
                                            $e ++;

                                            // COUNTRY DOESN'T HAVE COUNTIES
                                            if (!isset($all_levels_decoded['country'][ $country_name4 ]))
                                            {
                                                $countries_with_counties_with_l5__by_continent[ $continent4 ][ $country_name4 ] = [];
                                            }

                                            foreach ($counties4 as $county_name4 => $counties_level_4)
                                            {
                                                $e ++;
                                                // COUNTY DOESN'T HAVE LEVEL_4 COUNTIES
                                                if (empty($counties_level_4))
                                                {
                                                    $countries_with_counties_with_l5__by_continent[ $continent4 ][ $country_name4 ][ $county_name4 ] = [];

                                                }
                                                foreach ($counties_level_4 as $key => $county_level_4_name)
                                                {
                                                    $e ++;
                                                    if (isset($all_levels_decoded['level_5'][ $county_level_4_name ]))
                                                    {
                                                        $countries_with_counties_with_l5__by_continent[ $continent4 ][ $country_name4 ][ $county_name4 ][ $county_level_4_name ] = $all_levels_decoded['level_5'][ $county_level_4_name ];
                                                    } else
                                                    {
                                                        $countries_with_counties_with_l5__by_continent[ $continent4 ][ $country_name4 ][ $county_name4 ][ $county_level_4_name ] = [];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                      ///// all good here
                                                    // to be continued level_6
                                                    foreach($countries_with_counties_with_l5__by_continent as $continent5 => $countries5)
                                                    {
                                                        $e++;
                                                        foreach ($countries5 as $country_name5 => $counties5)
                                                        {
                                                            $e++;
                                                            // COUNTRY DOESN'T HAVE COUNTIES
                                                            if (!isset($all_levels_decoded['country'][ $country_name5 ]))
                                                            {
                                                                $countries_with_counties_with_l6__by_continent[ $continent5 ][ $country_name5 ] = [];
                                                            }
                                                            foreach ($counties5 as $county_name5 => $counties_level_4)
                                                            {
                                                                $e++;
//                                                                // COUNTY DOESN'T HAVE LEVEL_4 COUNTIES
                                                                if(empty($counties_level_4))
                                                                {
                                                                    $countries_with_counties_with_l6__by_continent[ $continent5 ][ $country_name5 ][ $county_name5 ] = [];

                                                                }
                                                                foreach ($counties_level_4 as $county_level_4_name => $counties_level_5)
                                                                {
                                                                    $e++;
                                                                    // COUNTY DOESN'T HAVE LEVEL_5 COUNTIES
                                                                    if(empty($counties_level_5))
                                                                    {
                                                                        $countries_with_counties_with_l6__by_continent[ $continent5 ][ $country_name5 ][ $county_name5 ][ $county_level_4_name ] = [];

                                                                    }
                                                                    foreach($counties_level_5 as $key2 => $county_level_5_name)
                                                                    {
                                                                        $e++;
                                                                        ///LEVEL_5_COUNTY DOESN'T HAVE LEVEL_6 COUNTIES
                                                                        // dd(empty($all_levels_decoded['level_6'][ $county_level_5_name ]) == true);


                                                                        if (isset($all_levels_decoded['level_6'][ $county_level_5_name ]))
                                                                        {
                                                                            $countries_with_counties_with_l6__by_continent[ $continent5 ][ $country_name5 ][ $county_name5 ][ $county_level_4_name ][ $county_level_5_name ] = $all_levels_decoded['level_6'][ $county_level_5_name ];
                                                                        }
                                                                        else
                                                                        {
                                                                            $countries_with_counties_with_l6__by_continent[ $continent5 ][ $country_name5 ][ $county_name5 ][ $county_level_4_name ][ $county_level_5_name ]= [];
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

     //  DB::table('test_all_levels')->insert(['level_name' =>'all_in_one', 'levels' => json_encode($countries_with_counties_with_l6__by_continent)]);
      // file_put_contents('all_in_one.txt', var_export ($countries_with_counties_with_l6__by_continent, true));
    // dd($countries_with_counties_with_l6__by_continent);
    $countries_names = [];
        foreach($countries_with_counties_with_l6__by_continent as $continent_name => $continent)
        {
            foreach($continent as $country=>$country_levels)
            {
               // DB::table('test_all_levels')->insert(['level_name' =>$country, 'levels' => json_encode($country_levels)]);
                array_push($countries_names, $country);
            }

        }
       DB::table('test_all_levels')->insert(['level_name' =>'countries_names', 'levels' => json_encode($countries_names)]);
     //  dd($countries_names);
    }
    public function get_world()
    {


        /// FUNCTION TO COUNT DIMENSIONS OF MULTIDIMENSIONAL ARRAY
       /* $a["one"]["two"]["three"]="1";

        function count_dimension($Array, $count = 0) {
            if(is_array($Array)) {
                return count_dimension(current($Array), ++$count);
            } else {
                return $count;
            }
        }

        dd(count_dimension($a));*/


        ini_set('max_execution_time', 3600);
        $world = $this->repository->world2();
//dd($world);
        $continents = [];

        $countries = [];
        $countries_array = [];

        $counties =[];
        $counties_array = [];

        $counties_L4 =[];
        $counties_L4_array =[];

        $counties_L5 =[];
        $counties_L5_array =[];

        $counties_L6 =[];
        $counties_L6_array =[];

        $world_with_continents_array = [];
        $continents_with_countries_array = [];
        $countries_with_counties_array = [];
        $counties_with_countiesL4_array = [];
        $counties_with_countiesL5_array = [];
        $counties_with_countiesL6_array= [];


        $big_countries_names =[];
        $big_countries_country_code =[];

        $big_countries = array('0402' ,'0706','0404','1107' ,'1002' ,'0506' ,'0540','0510' ,'0127' ,'0407' ,'0704' ,'0515' ,'0539','1104','0508','0702','1202','1205','0527','1201','0155' ,'0406','0804' ,'1005' ,'1207','0149','0525','1001' ,'1208','0608','0552','1102','1108','0602');
        foreach($big_countries as $big_country)
        {
            if ($big_country[0] == 0)
            {
                $cont = $big_country[1];
            } else
            {
                $cont = $big_country[0] . $big_country[1];
            }
            if ($big_country[2] == 0)
            {
                $country = $big_country[3];
            } else
            {
                $country = $big_country[2] . $big_country[3];
            }


            array_push($big_countries_country_code, $cont . '.' . $country);
        }

        foreach($world as $code => $path)
        {
            if(sizeof(explode('.',$code)) == 1)
            {
                $continents[$code] = $path;
            }

            if(sizeof(explode('.',$code)) == 2)
            {

                if(in_array($code,$big_countries_country_code))
                {
                    array_push( $big_countries_names,$path);


                }

                $countries[$code] = $path;



            }

            if(sizeof(explode('.',$code)) == 3 )
            {
                $counties[$code] = $path;

            }
            if(sizeof(explode('.',$code)) == 4 )
            {
                $counties_L4[$code] = $path;

            }
            if(sizeof(explode('.',$code)) == 5 )
            {
                $counties_L5[$code] = $path;

            }
            if(sizeof(explode('.',$code)) == 6 )
            {
                $counties_L6[$code] = $path;

            }

        }
       // dd($big_countries_names);
      /*  dd($big_countries_names);*/
      //  DB::table('test_all_levels')->delete();
        $table_name = 'test_all_levels';
        // to get world with $continents
      foreach($continents as $continent_code => $continent_name)
        {

           // array_push($world_with_continents_array,$continent_name);
            $world_with_continents_array[$continent_code]=$continent_name;

        }
       
      DB::table($table_name)->insert(['level_name' =>'world', 'levels' => json_encode($world_with_continents_array)]);
       // dd($world_with_continents_array);
    
        // to get $continents with countries
        foreach($continents as $continent_code => $continent_name)
        {

            foreach($countries as $country_code => $country_name)
            {
                if(     explode('.',$continent_code)[0] == explode('.',$country_code)[0]  )

                {

                    /*array_push($countries_array,[$country_code=>$country_name]);*/
                   // array_push($countries_array,[$country_code=>$country_name]);
                    $countries_array[$country_code] =   $country_name;
                }
            }
            if(isset($continent_name))
            {
                $continents_with_countries_array[$continent_code] = $countries_array;

            }


            $countries_array = [];
        }
      // dd($continents_with_countries_array);
    DB::table($table_name)->insert(['level_name' =>'continent', 'levels' => json_encode($continents_with_countries_array)]);
        //dd( $continents_with_countries_array);

     // to get countries with counties
       foreach($countries as $country_code => $country_name)
        {

            foreach($counties as $county_code => $county_name)
            {

                if(     explode('.',$country_code)[0] == explode('.',$county_code)[0]  &&
                        explode('.',$country_code)[1] == explode('.',$county_code)[1])
                {

                  //array_push($counties_array,$county_name);
                    $counties_array[$county_code] =   $county_name;
                }
            }


            if(isset($country_name) && !empty($counties_array))
            {
                $countries_with_counties_array[$country_code] = $counties_array;

            }
            /*else
            {
                $countries_with_counties_array[$country_name] = [];
            }*/



            $counties_array = [];
        }
      
      DB::table($table_name)->insert(['level_name' =>'country', 'levels' => json_encode($countries_with_counties_array)]);
      //  dd( $countries_with_counties_array);
      

        // to get  counties with counties_L4



           foreach($counties as $county_code => $county_name)
            {
                foreach($counties_L4 as $county_L4_code => $county_L4_name)
                {
                    if (explode('.', $county_code)[0] == explode('.', $county_L4_code)[0] &&
                        explode('.', $county_code)[1] == explode('.', $county_L4_code)[1] &&
                        explode('.', $county_code)[2] == explode('.', $county_L4_code)[2]
                    )
                    {
                        //array_push($counties_L4_array, $county_L4_name);
                        $counties_L4_array[$county_L4_code] =   $county_L4_name;
                    }
                }
                //BECAUSE SOME COUNTRIES HAVE NO COUNTIES
                if(isset($county_name)  && !empty($counties_L4_array))
                {
                    $counties_with_countiesL4_array[$county_code] = $counties_L4_array;

                }
               /* else
                {
                    $counties_with_countiesL4_array[$county_name] = [];
                }*/

                $counties_L4_array = [];
            }
    
       // dd($counties_with_countiesL4_array);
    DB::table($table_name)->insert(['level_name' =>'level_4', 'levels' => json_encode($counties_with_countiesL4_array)]);
        //dd($counties_with_countiesL4_array);
        
        //// to get level4 with level5 counties
      
     foreach($counties_L4 as $county_L4_code => $county_L4_name)
        {
            foreach($counties_L5 as $county_L5_code => $county_L5_name)
            {

                if (explode('.', $county_L4_code)[0] == explode('.', $county_L5_code)[0] &&
                    explode('.', $county_L4_code)[1] == explode('.', $county_L5_code)[1] &&
                    explode('.', $county_L4_code)[2] == explode('.', $county_L5_code)[2] &&
                    explode('.', $county_L4_code)[3] == explode('.', $county_L5_code)[3]
                )
                {
                   // array_push($counties_L5_array, $county_L5_name);
                    $counties_L5_array[$county_L5_code] =   $county_L5_name;
                }
            }

            //BECAUSE SOME COUNTRIES HAVE NO COUNTIES_L4
            if(isset($county_L4_name)   && !empty($counties_L5_array))
            {
                $counties_with_countiesL5_array[$county_L4_code] = $counties_L5_array;

            }
           /* else
            {
                $counties_with_countiesL5_array[$county_L4_name] = [];
            }*/

            $counties_L5_array = [];
        }
        
    DB::table($table_name)->insert(['level_name' =>'level_5', 'levels' => json_encode($counties_with_countiesL5_array)]);
    
      // dd($counties_with_countiesL5_array);
      

        
        foreach($counties_L5 as $county_L5_code => $county_L5_name)
        {
            foreach($counties_L6 as $county_L6_code => $county_L6_name)
            {

                if (explode('.', $county_L5_code)[0] == explode('.', $county_L6_code)[0] &&
                    explode('.', $county_L5_code)[1] == explode('.', $county_L6_code)[1] &&
                    explode('.', $county_L5_code)[2] == explode('.', $county_L6_code)[2] &&
                    explode('.', $county_L5_code)[3] == explode('.', $county_L6_code)[3] &&
                    explode('.', $county_L5_code)[4] == explode('.', $county_L6_code)[4]
                )
                {
                   // array_push($counties_L6_array, $county_L6_name);
                    $counties_L6_array[$county_L6_code] =   $county_L6_name;
                }
            }

            //BECAUSE SOME COUNTRIES HAVE NO COUNTIES_L6
            if(isset($county_L5_name)   && !empty($counties_L6_array))
            {
                $counties_with_countiesL6_array[$county_L5_code] = $counties_L6_array;
            }
           /* else
            {
                $counties_with_countiesL6_array[$county_L5_name] = [];
            }*/

            $counties_L6_array = [];
        }
        
      DB::table($table_name)->insert(['level_name' =>'level_6', 'levels' => json_encode($counties_with_countiesL6_array)]);
       // dd( $counties_with_countiesL6_array);
      
dd('ALL INSERTED');





    }
    
    /// languages, currencies. iso , seifex_id ...
    public function country_data()
    {
        $all = DB::table('countries')
            ->get([ 'country_data', 'country_name', 'seifex_country_id' ])
            ->toArray();
       
        $iso = [];
        foreach ($all as $country => $data) {
            
            $iso[ json_decode($data->country_data, true)[ 'alpha2' ] ] = $data->seifex_country_id;
            $seifex_ids[$data->seifex_country_id]   =   json_decode($data->country_data, true)[ 'alpha2' ];
        }
       
        /////////////////////// getting info from text file

        $web_source = 'http://download.geonames.org/export/dump/countryInfo.txt';
        $local_source = 'C:\wamp\www\seifex\resources\world_info\countryInfo.txt';
        $headers=get_headers($web_source);
        // dd($headers);


        if (strpos($headers[0], '200 OK') !== false) {
            $source =   $web_source;
        }
        else{
            $source =   $local_source;
        }


        $lines = file($source, FILE_IGNORE_NEW_LINES) ;


        $first_line=0;
        foreach ($lines as $key =>  $line)
        {
            if($line[0] == '#')
            {
                $first_line++;
            }


        }

        $strings    =  preg_split("/[\t]/", $lines[ ($first_line  == 0 ?  0 :  $first_line-1)  ]);
        $strings[0]  =   str_replace('#','',$strings[0]);

        foreach ($lines as $key =>  $line)
        {


            if($key     >  ($first_line  == 0 ?  -1 :  $first_line-1) )
            {

                $web_data [preg_split("/[\t]/", $line)[0]] = array_combine($strings,preg_split("/[\t]/", $line))  ;
            }
        }


       
        /////////////////////// end of getting info from text file
        ///
        /// currency symbols
        $currency_symbols_array   =   file('C:\wamp\www\seifex\resources\world_info\currency_symbol.csv', FILE_IGNORE_NEW_LINES);
        
        foreach ($currency_symbols_array as $currency_symbol) {
            
            $currency_symbols[  str_replace('"','',explode(';',$currency_symbol)[0])  ]
                = str_replace('"','',explode(';',$currency_symbol)[1]);
        }
       
        ///  end of currency symbols
     
        
        $data = DB::table('test_all_levels')
            ->where('level_name', 'countries_info')
            ->pluck('levels')
            ->first();
      
        $languages_by_currency = [];
        $languages_by_country_iso = [];
        $languages_by_country_seifex_id = [];
        $neighbours_by_country_seifex_id = [];
        $country_seifex_id_to_country_iso = [];
        $country_seifex_id_to_country_seifex_name = [];
        $new_data   =   [];
        $countries_by_currency = [];
        $currencies_by_country_seifex_id    =   [];
        $all_currencies=[];
        $currencies_full = [];
      //dd($currency_symbols);
        foreach (json_decode($data, true) as $iso_code => $country_info) {
           $currency_symbol=    isset($currency_symbols[$country_info['CurrencyCode']])
               ? $currency_symbols[$country_info['CurrencyCode']] : '1';
            if (isset($iso[ $iso_code ])) {
               // dd($country_info);
                $languages_by_currency_unsorted[ $country_info[ 'CurrencyCode' ] ] [] = $country_info[ 'Languages' ];
                $languages_by_country_iso_unsorted[ $country_info[ 'ISO' ] ] [] = $country_info[ 'Languages' ];
                $languages_by_country_seifex_id_unsorted[ $iso[ $iso_code ] ][] = $country_info[ 'Languages' ];
                $currencies_by_country_seifex_id[ $iso[ $iso_code ] ] = $country_info[ 'CurrencyCode' ];
                $all_currencies[$country_info[ 'CurrencyCode' ]]    =  $currency_symbol;
                
                $currencies_full[$country_info[ 'CurrencyCode' ]] =$country_info[ 'CurrencyCode' ].'_'. $country_info[ 'CurrencyName' ].' ( '.
                    $currency_symbol.' )';
                
                $country_seifex_id_to_country_iso[  $iso[ $iso_code ]   ]   =   $iso_code;
                $new_data[ $iso[ $iso_code ]    ]   =   $country_info;
                $new_data[ $iso[ $iso_code ]    ] ['CurrencySymbol']  = isset($currency_symbols[$country_info['CurrencyCode']])
                    ? $currency_symbols[$country_info['CurrencyCode']] : '';
                $countries_by_currency[$country_info['CurrencyCode'] ][] = $iso[ $iso_code ];
                
                foreach (explode(',', $country_info[ 'neighbours' ]) as $neighbour) {
                    
                    if (isset($iso[ $neighbour ])) {
                        $neighbours_by_country_seifex_id[ $iso[ $iso_code ] ][] = $iso[ $neighbour ];
                        
                    } elseif ($neighbour == null) {
                        ///// countries with no neighbour
                        $no_neighbours_country[] = $iso[ $iso_code ] . ' - ' . $country_info[ 'Country' ];
                    } else
                        
                        //////$neighbour =  iso code XK,EH  of old/disolved countries   that doesn't have neighbours
                        $errors[ 'neighbours_of_old_disolved_countries' ][] =
                            $neighbour . ' - was neighbour to : ' .
                            $country_info[ 'Country' ] . ' - ' .
                            $iso[ $iso_code ];
                    
                }
            } else {
                ///// seifex doesn't have this locations ( islands and territories...)
                $errors[ 'not_in_seifex' ][ $iso_code ] = $country_info;
            }
            
        }
      
       $all_languages=[];
        foreach ($languages_by_currency_unsorted as $currency => $languages) {
            
            foreach ($languages as $language_string) {
                $languages = explode(',', $language_string);
                
                foreach ($languages as $language) {
                    $languages_by_currency[ $currency ][ explode('-', $language)[ 0 ] ] = \Locale::getDisplayLanguage($language, $language);
                    if(explode('-', $language)[ 0 ] != '')
                    $all_languages[explode('-', $language)[ 0 ]] = \Locale::getDisplayLanguage($language, $language);
                }
            }
        }
        asort($all_languages,SORT_REGULAR);
    
        foreach ($languages_by_country_iso_unsorted as $country_iso => $languages) {
            
            foreach ($languages as $language_string) {
                $languages = explode(',', $language_string);
                
                foreach ($languages as $language) {
                    $languages_by_country_iso[ $country_iso ][ $language ] = \Locale::getDisplayLanguage($language, $language);
                }
            }
        }
        
       
        foreach ($languages_by_country_seifex_id_unsorted as $country_seifex_id => $languages) {
            
            foreach ($languages as $language_string) {
                $languages = explode(',', $language_string);
                
                foreach ($languages as $language) {
                    $languages_by_country_seifex_id[ $country_seifex_id ][ explode('-',$language)[0] ] =  \Locale::getDisplayLanguage($language, $language);
                }
            }
        }
    
      
        $neighbours_languages=[];
        $my_neighbours_languages=[];
        $neighbours_currencies=[];
        $my_neighbours_currencies=[];
        
        foreach($neighbours_by_country_seifex_id as $country=>$neighbours)
        {
            foreach($neighbours as $neighbour)
            {
              
                $neighbours_languages[$country][$neighbour] = $languages_by_country_seifex_id[$neighbour];
                $neighbours_currencies[$country][$neighbour] = $currencies_by_country_seifex_id[$neighbour];
            }
           
        }
       
        foreach($neighbours_languages as $country => $n_languages)
        {
            foreach($n_languages as $neigh => $langs)
            {
                foreach($langs as $lang => $key)
                {
                    $my_neighbours_languages[$country][explode('-',$lang)[0]]   =   \Locale::getDisplayLanguage($lang, $lang);
                    
                    foreach($languages_by_country_seifex_id[$country] as $lang_short    =>  $lang_name)
                    {
                        if (isset($my_neighbours_languages[$country][$lang_short])) {
                            ////// unseting home country languages
                            unset($my_neighbours_languages[$country][$lang_short]);
                        }
                    }
                    
                }
            }
        }
     
       
        
        foreach($neighbours_currencies as $country => $n_currencies)
        {
            $my_neighbours_currencies[$country]  = array_unique( array_values($n_currencies));
    
            if (($country_currency = array_search($currencies_by_country_seifex_id[$country], $my_neighbours_currencies[$country])) !== false) {
                ////// unseting home country currency
                unset($my_neighbours_currencies[$country][$country_currency]);
            }
        }
    
       
        $countries_info_by_country_id=[];
        $non_seifex_countries_info=[];
        foreach($web_data as $iso => $data)
        {
            if(isset(array_flip($country_seifex_id_to_country_iso)[$iso]))
            {
                $countries_info_by_country_id[array_flip($country_seifex_id_to_country_iso)[$iso]] =  $data;
            }
            else
            {
                $countries_info_by_country_id[$iso]=  $data;
                $non_seifex_countries_info[$iso]  =   $data;
            }
        }
       
   //  dd($my_neighbours_languages,$my_neighbours_currencies,$languages_by_country_seifex_id);
        $insert_data    =      [
            'currencies_with_data'=>json_encode($currencies_full)
           /* 'non_seifex_countries_info'=>json_encode($non_seifex_countries_info),
            'countries_info_by_country_id'  =>json_encode($countries_info_by_country_id),*/
           /* 'all_languages'=>json_encode($all_languages),*/
           /* 'all_currencies'=>json_encode($all_currencies),*/
           /* 'my_neighbours_languages'=>json_encode($my_neighbours_languages),
            'my_neighbours_currencies'=>json_encode($my_neighbours_currencies),
            'languages_by_country_seifex_id' => json_encode($languages_by_country_seifex_id)*//*,
            'currencies_by_country_seifex_id'=>$currencies_by_country_seifex_id,
            'countries_by_currency'=> json_encode($countries_by_currency),
            'country_data'  =>     json_encode( $new_data),
            'currency_symbols'=> json_encode($currency_symbols),
            'languages_by_currency' => json_encode($languages_by_currency),
            'languages_by_country_iso' => json_encode($languages_by_country_iso),
            'languages_by_country_seifex_id' => json_encode($languages_by_country_seifex_id),
            'neighbours_by_country_seifex_id' => json_encode($neighbours_by_country_seifex_id),
            'country_seifex_id_to_country_iso'=> json_encode($country_seifex_id_to_country_iso),
            'country_iso_to_country_seifex_id'=> json_encode($iso),*/
        ];
    
        
   
    /*  DB::table('test_all_levels')->insert(['level_name'=> 'currencies_by_country_seifex_id',
            'levels'    =>  json_encode($currencies_by_country_seifex_id)]);*/
        foreach($insert_data as $level_name => $levels)
		  {
			  $insert[] = ['level_name'   =>  $level_name,'levels'=> $levels];
			 
		  }
    
    
// DB::table('test_all_levels')->insert($insert);
        dd('INSERTED f');
        return $all;
        
    }
    
    public function make_locations_table()
    {
//        $local_source = 'C:\wamp\www\seifex\resources\world_info\world.txt';
//        $lines = file($local_source, FILE_IGNORE_NEW_LINES) ;
//
//        foreach ($lines as $key =>  $line)
//        {
//            $web_data [] = preg_split("/[\t]/", $line)  ;
//        }
//        foreach($web_data as $key2 => $data)
//        {
//
//            $world [
//
//                str_replace(',','',
//                    str_replace("'","",
//                        explode('=>',$data[sizeof($data) - 1]) [1]))
//
//            ]   =
//                str_replace(' ','',
//                    str_replace("'","", explode('=>',$data[sizeof($data) - 1]) [0]) )
//                 ;
//        }
//
//        file_put_contents('C:\wamp\www\seifex\resources\world_info\world_fixed.txt', var_export( $world, true));
//        dd('done');
        
        $world_array        = $this->repository->world() ;
      // dd($world_array,sizeof(array_flip($world_array)),sizeof($world_array));
        $world_to_insert    =   [];
        $multi_world    =   [];
        $multi_world_double_names    =   [];
        /*
  "Província-de-Cabinda" => "Africa : Angola : Província de Cabinda?1.1.4"
  "Província-de-Cuando-Cubango" => "Africa : Angola : Província de Cuando Cubango?1.1.5"
       
       'Лукояновский-район--Lukoyanovsky-District'=>'Russia : Приволжский ф.о. # Volga Federal District : Нижегородская область # Nizhny Novgorod Oblast : Лукояновский район # Lukoyanovsky District?9.2.2.30',
	'Лысковский-район'=>'Russia : Приволжский ф.о. # Volga Federal District : Нижегородская область # Nizhny Novgorod Oblast : Лысковский район # Lyskovsky District?9.2.2.31',
	*/
        $country_seifex_id_name=[];
        foreach ($world_array as $location_data =>  $location_name)
        {
       
            $location_ids_array = explode('.',explode('?',$location_data)[1]);
            
            if(sizeof( $location_ids_array )  >  1)
            {
                
                if(  sizeof( explode('.',explode('?',$location_data)[1]) )  ===  2 ) ///// country
                {
                    
                    $country_id    =   explode('?',$location_data)[1];
                    $county_id     =   '';
                    $county_l4_id  =   '';
                    $county_l5_id  =   '';
                    $county_l6_id  =   '';
                    $country_seifex_id_name[$country_id] = $location_name;
                    
                }
                
                if(  sizeof( explode('.',explode('?',$location_data)[1]) )  ===  3 ) ///// county
                {
                    
                    $country_id    =   $location_ids_array[0].'.'.$location_ids_array[1];
                    $county_id     =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2];
                   
                    $county_l4_id  =   '';
                    $county_l5_id  =   '';
                    $county_l6_id  =   '';
                }
                
                if(  sizeof( explode('.',explode('?',$location_data)[1]) )  ===  4) ///// county_l4
                {
                    
                    
                    $country_id    =   $location_ids_array[0].'.'.$location_ids_array[1];
                    $county_id     =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2];
                    $county_l4_id  =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2].'.'.$location_ids_array[3];
                    $county_l5_id  =   '';
                    $county_l6_id  =   '';
                }
                if(  sizeof( explode('.',explode('?',$location_data)[1]) )  ===  5) ///// county_l5
                {
                    
                    
                    $country_id    =   $location_ids_array[0].'.'.$location_ids_array[1];
                    $county_id     =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2];
                    $county_l4_id  =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2].'.'.$location_ids_array[3];
                    $county_l5_id  =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2].'.'.$location_ids_array[3].'.'.$location_ids_array[4];
                    $county_l6_id  =   '';
                }
                if(  sizeof( explode('.',explode('?',$location_data)[1]) )  ===  6) ///// county_l6
                {
                    
                    
                    $country_id    =   $location_ids_array[0].'.'.$location_ids_array[1];
                    $county_id     =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2];
                    $county_l4_id  =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2].'.'.$location_ids_array[3];
                    $county_l5_id  =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2].'.'.$location_ids_array[3].'.'.$location_ids_array[4];
                    $county_l6_id  =   $location_ids_array[0].'.'.$location_ids_array[1].'.'.$location_ids_array[2].'.'.$location_ids_array[3].'.'.$location_ids_array[4].'.'.$location_ids_array[5];
                }
                $path  =  explode(' : ',explode('?',$location_data)[0])  ;
                array_pop($path);
               
               
                $multi_world[explode('?',$location_data)[1]][$location_name][]=
                    [
                        'location_id'           =>          explode('?',$location_data)[1],
                        'location_name'         =>          $location_name,
                        'country_id'            =>          $country_id,
                        'county_id'             =>          $county_id,
                        'county_l4_id'          =>          $county_l4_id,
                        'county_l5_id'          =>          $county_l5_id,
                        'county_l6_id'          =>          $county_l6_id,
                        'path'                  =>          implode(' : ',$path),
                        'created_at'            =>          date('Y-m-d'),
                        'updated_at'            =>          date('Y-m-d'),
                    ];
                $world_to_insert[]=
                    [
                        'location_id'           =>          explode('?',$location_data)[1],
                        'location_name'         =>          $location_name,
                        'country_id'            =>          $country_id,
                        'county_id'             =>          $county_id,
                        'county_l4_id'          =>          $county_l4_id,
                        'county_l5_id'          =>          $county_l5_id,
                        'county_l6_id'          =>          $county_l6_id,
                        'path'                  =>          implode(' : ',$path),
                        'created_at'            =>          date('Y-m-d'),
                        'updated_at'            =>          date('Y-m-d'),
                    ];
                /*$world_unique[$location_name]  =
                    [
                        'location_id'           =>          explode('?',$location_data)[1],
                        'location_name'         =>          $location_name,
                        'country_id'            =>          $country_id,
                        'county_id'             =>          $county_id,
                        'county_l4_id'          =>          $county_l4_id,
                        'county_l5_id'          =>          $county_l5_id,
                        'county_l6_id'          =>          $county_l6_id,
                        'path'                  =>          implode(' : ',$path),
                        'created_at'            =>          date('Y-m-d'),
                        'updated_at'            =>          date('Y-m-d'),
                    ];*/
            }
        }
        ini_set('max_execution_time', 800);
       
//       foreach($multi_world as $loc_id=>$multi_names)
//       {
//           $location =  array_pop($multi_names)[0];
//
//           $multi_world_double_names[$location['location_name']] =   $location;
//
//          /* $multi_world_double_names_insert[]   =
//               [
//                   $location
//               ];*/
//       }
      //  dd($multi_world_double_names);
   //  dd(/*sizeof($world_unique),sizeof($world_array),sizeof($nulti_world)*/$multi_world_double_names_insert);
     /* ini_set('max_execution_time', 600);*/
        foreach($world_to_insert as  $data)
        {
            DB::table('locations2')->insert($data);
        }
        dd('locations table populated ..;-)');
       
    }
    /*
     *  $roles      =   DB::table('roles')->get(DB::raw('CONCAT(guard, "_", name) as role'))->pluck('role')->toArray();
     *
     *
     * */
}
