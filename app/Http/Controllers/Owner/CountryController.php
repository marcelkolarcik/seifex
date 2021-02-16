<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Services\Currency;
use App\Services\LocationNameOrId;

class CountryController extends Controller
{

    protected $redirectTo = '/owner/login';
    public $currency;
    public $location;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct( LocationNameOrId $location, Currency $currency)
    {
        $this->middleware('owner.auth:owner');
        $this->location =  $location;
        $this->currency  =   $currency;
    }

    /**
     * Show the Owner dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function current() {
        
        $countries_active   =   'active';
        $current_country_ids   =   $this->current_countries_id();
        $countries  =   $this->get_countries_by_continet($current_country_ids);
        
        return view('owner.countries.current',compact('countries_active','countries'));
    }
    public function add_country()
    {
        $add_country_active   =   'active';
        $country_ids   =   DB::table('countries')->where('started_at','=',null)->pluck('seifex_country_id')->toArray();
        $countries  =   $this->get_countries_by_continet($country_ids);
        
        return view('owner.countries.add_country',compact('add_country_active','countries'));
    }
    public function remove_country()
    {
        $remove_country_active   =   'active';
        $current_country_ids   =   $this->current_countries_id();
        $countries  =   $this->get_countries_by_continet($current_country_ids);
        
        return view('owner.countries.remove_country',compact('remove_country_active','countries'));
    }
    public function new_requests()
    {
        $new_requests_active    =   'active';
        $countries  =   DB::table('new_country_requests')->where('confirmed_at','!=',null)->get('country_name')
           
           ->groupBy('country_name') ->toArray();
        array_multisort(array_map('count', $countries), SORT_DESC, $countries);
        
        return view('owner.countries.new_requests',compact('countries','new_requests_active'));
       
    }
    public function remove_country_post(Request $request)
    {
        if($request->ids  ==  null)   return back()->with('no_country_selected','true');
        
        $seifex_currencies  =   $this->currency->get_seifex_currencies('true');
        
        foreach ($request->ids as $country_seifex_id)
        {
            $currency   = $this->currency->get_country_currency($country_seifex_id);
           
            unset($seifex_currencies[$currency][$country_seifex_id]);
    
            if($seifex_currencies[$currency] == []) unset($seifex_currencies[$currency]);
    
            $this->update_countries($country_seifex_id);
        }
    
        $this->update_seifex_currencies($seifex_currencies);
       
        return back()->with('country_removed',$this->get_countries($request->ids));
    }
    public function store(Request $request)
    
    {
       
        if($request->ids  ==  null)   return back()->with('no_country_selected','true');
      
        foreach ($request->ids as $country_seifex_id)
        {
            $currency   = $this->currency->get_country_currency($country_seifex_id);
           
            $currencies[$currency][$country_seifex_id] = 1;
    
            $this->update_countries($country_seifex_id,date('Y-m-d H:i:s'));
        }
    
        
        
        $seifex_currencies  =   $this->currency->get_seifex_currencies('true');
     
        if($seifex_currencies === null)/// first currency
        {
            $seifex_currencies = $currencies;
        }
        else{
            $seifex_currencies =   array_merge_recursive($currencies,$seifex_currencies);
        }
       
       $this->update_seifex_currencies($seifex_currencies);
       
        return back()->with('country_added',$this->get_countries($request->ids));
    }
    
    private function update_seifex_currencies($seifex_currencies)
    {
        DB::table('test_all_levels')
            ->updateOrInsert(
                [   'level_name'     =>      'currencies'],
                [
                    'levels'         =>      json_encode($seifex_currencies),
                    'updated_at'     =>      date('Y-m-d H:i:s')
                ]
            );
       
    }
    private function update_countries($country_seifex_id,$date = null)
    {
        DB::table('countries')
            ->where('seifex_country_id',$country_seifex_id)
            ->update([  'started_at'        =>  $date,
            ]);
    }
    private function get_countries_by_continet(array $ids)
   {
       return $this->location->get_countries_by_continet($ids);
   }
    private function get_countries(array $ids)
    {
        return $this->location->get_countries($ids);
    }
    private function current_countries_id()
    {
        return $this->location->current_countries_id();
    }
}
