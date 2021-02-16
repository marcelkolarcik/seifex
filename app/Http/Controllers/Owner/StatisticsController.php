<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Repository\LocationIdRepository;

class StatisticsController extends Controller
{
    
    public function __construct(LocationIdRepository $locationIdRepository  )
    {
        $this->middleware('owner.auth:owner');
        $this->locationIdRepository =   $locationIdRepository;
    }
    
    
   public function index()
   {
       $turnover  =   DB::table('country_purchase_sales_statistics')->sum('order_value');
       
      
       $countries_active    =   'active';
        $page =   'Statistics ';
       return view('owner.statistics.countries', compact('turnover','page','countries_active'));
   }
   public function countries($type)
   {
       $stats  =   DB::table('country_purchase_sales_statistics')->get([$type.'_country','order_value'])->groupBy($type.'_country')->toArray();
       $type_active     =   $type.'_active';
       $$type_active =   'active';
       
    
       $countries   =   [];
       foreach ($stats as $country_id => $list) {
    
           $countries[$this->locationIdRepository->idLocation($country_id)]    =  ['order_value'    =>  $list[0]->order_value, 'country_id' =>  $country_id] ;
       }
       arsort($countries);
       $page =   'Statistics ';
       return view('owner.statistics.countries', compact('countries','page','type_active','type'));
   }
   public function country($type,$country_id)
   {
       $companies   =   DB::table('seifex_orders')
           ->where($type.'_country',$country_id)
           ->groupBy($type.'_company_id')
           ->selectRaw('sum(order_value) as sum, '.$type.'_company_id')
           ->pluck('sum',$type.'_company_id')->toArray();
       
//       Document::groupBy('users_editor_id')
//           ->selectRaw('sum(no_of_pages) as sum, users_editor_id')
//           ->pluck('sum','users_editor_id');
       
       $company_names   =   DB::table($type.'_companies')
           ->whereIn('id',array_keys($companies))->pluck($type.'_company_name','id')->toArray();
       arsort($companies);
      
       $page =   'Statistics ';
       $country_active  =   'active';
       $country    =   $this->locationIdRepository->idLocation($country_id);
       
       return view('owner.statistics.country', compact('companies','company_names','page','country_active','type','country','country_id'));
   }
    public function company($type,$country_id,$company_id)
    {
        $stats  =   DB::table('purchase_sales_statistics')->where($type.'_company_id',$company_id)->pluck('product_list','department')->toArray();
        $statistics   =   [];
        foreach ($stats as $department => $list) {
            $statistics  [$department]    =   json_decode($list,true);
        }
        $page =   'Statistics ';
        $company_active  =   'active';
        $company =   DB::table($type.'_companies')->where('id',$company_id)->pluck($type.'_company_name')->first();
        $country    =   $this->locationIdRepository->idLocation($country_id);
       
        return view('owner.statistics.company', compact('statistics','company_active','page','type','company','company_id','country','country_id'));
    }
}
