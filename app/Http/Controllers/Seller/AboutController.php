<?php

namespace App\Http\Controllers\Seller;

use App\Services\Currency;
use App\Services\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Repository\NavigationRepository;
use App\Repository\LocationIdRepository;
use App\Services\Company;
use App\Services\Role;

class AboutController extends Controller
{
    public $company;
    public $role;
    public function __construct(Role $role, Company $company, NavigationRepository $navigationRepository,LocationIdRepository $locationIdRepository )
    {
        $this->navigationRepository = $navigationRepository;
        $this->company = $company;
        $this->role = $role;
        $this->locationIdRepository = $locationIdRepository;
        $this->middleware('seller.auth:seller');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function    companies()
    {
        return $this->navigationRepository->full_companies();
    }
    
    public function index()
    {
    
        $companies                  =   $this->company->for($this->role->get_owner_or_staff());
        $location   =   [];
        foreach($companies as $company)
        {
            $country    =   $this->locationIdRepository->IdLocation($company[0]->country);
            $county    =   $this->locationIdRepository->IdLocation($company[0]->county);
            $county_l4    =   $this->locationIdRepository->IdLocation($company[0]->county_l4);
    
            if(isset($country)) $location[$company[0]->id]['country']        =   $country;
            if(isset($county)) $location[$company[0]->id]['county']          =   $county;
            if(isset($county_l4)) $location[$company[0]->id]['county_l4']    =   $county_l4;
        }
    
        $sales   =   DB::table('purchase_sales_statistics')
            ->where('seller_company_id',array_keys($companies))
            ->get(['order_value','department','seller_company_id'])
           //->groupBy(['seller_company_id','department'])
            ->toArray() ;
        $all_sales      =   [];
        $order_value    =   0;
        $seller_company_ids =   [];
        $seller_company_departments=   [];
        /*https://www.redtube.com/13047531*/
       foreach($sales as $sale)
       {
           if(isset($seller_company_ids[sizeof($seller_company_ids) - 1]))
           {
               if($seller_company_ids[sizeof($seller_company_ids) - 1] != $sale->seller_company_id)
               {
                   $order_value =   0;
               }
               if($seller_company_departments[$sale->seller_company_id][sizeof($seller_company_departments[$sale->seller_company_id]) - 1] != $sale->department)
               {
                   $order_value =   0;
               }
           }
          
           $order_value += $sale->order_value;
           $seller_company_ids[]    =  $sale->seller_company_id;
           $seller_company_departments[$sale->seller_company_id][]    =  $sale->department;
           $all_sales[$sale->seller_company_id][$sale->department] = $order_value ;
           
           
          
           
       }
      // dd($all_sales);
        return view('seller.about.index',compact('companies','location','all_sales'));
       
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delivery_locations()
    {
        $companies                  =  $this->companies();
        $named_locations         =   DB::table('delivery_locations')
            ->whereIn('seller_company_id',array_keys($companies))
            ->get(['department','country','county','county_l4'])
            ->groupBy('department')
            ->toArray();
      // dd($named_locations);
        foreach($named_locations as $department  =>  $locations)
        {
            foreach($locations as $key  =>  $location)
            {
                $delivery_locations[$department][$key]['country']   =   str_replace('--',' | ',$this->locationIdRepository->IdLocation($location->country));
                $delivery_locations[$department][$key]['county']   =    str_replace('--',' | ',$this->locationIdRepository->IdLocation($location->county));
                $delivery_locations[$department][$key]['county_l4'] =    str_replace('--',' | ',$this->locationIdRepository->IdLocation($location->county_l4));
            }
            
        }
      // dd($delivery_locations);
        $delivery_locations_active  =   'active';
    
        return view('seller.about.delivery_locations',compact('delivery_locations','delivery_locations_active'));
    }
    public function prices()
    {
        $companies      =  $this->companies();
        $prices         =   DB::table('seller_extended_price_lists')->whereIn('seller_company_id',array_keys($companies))->pluck('price_list','department');
        $prices_active  =   'active';
       // dd($prices);
        return view('seller.about.prices',compact('prices','prices_active'));
    }
    public function our_buyers()
    {
        $companies                  =  $this->companies();
        
        $our_buyers   =   DB::table('purchase_sales_statistics')
            ->join('buyer_companies', 'purchase_sales_statistics.buyer_company_id', '=', 'buyer_companies.id')
            ->where('seller_company_id',array_keys($companies))
            ->get(['department','buyer_company_id','order_value','buyer_companies.buyer_company_name'])
            ->sortByDesc('order_value')
            ->groupBy('department')
            ->toArray();
    
        
        $our_buyers_active          =   'active';
        
        return view('seller.about.our_buyers',compact('our_buyers','our_buyers_active','orders'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
        $company    =  DB::table('buyer_companies')
            ->where('id',$id)->first();
       
        $currencies = Currency::get_name_for_company(json_decode($company->currencies,true) );
        $languages = Language::names_for_company(json_decode($company->languages,true)) ;
     
        $company_active = 'active';
        $our_buyers_active          =   'active';
        $location   =   [];
       
        $country    =   $this->locationIdRepository->IdLocation($company->country);
        $county    =   $this->locationIdRepository->IdLocation($company->county);
        $county_l4    =   $this->locationIdRepository->IdLocation($company->county_l4);
        
        if(isset($country)) $location['country']        =   $country;
        if(isset($county)) $location['county']          =   $county;
        if(isset($county_l4)) $location['county_l4']    =   $county_l4;
        $companies                  =  $this->companies();
        $sales   =   DB::table('purchase_sales_statistics')
            ->where('buyer_company_id',$id)
            ->where('seller_company_id',array_keys($companies))
            ->pluck('order_value','department')
            ->toArray();
    
        $their_sellers   =   DB::table('seller_companies')
            ->whereIn('id',array_keys($companies))
            ->pluck('seller_company_name','id');
        
        return view('seller.about.buyer',compact(
            'company',
            'company_active',
            'our_buyers_active',
            'location',
            'sales',
            'their_sellers',
            'currencies',
            'languages'
        ));
    }
    public function show_seller($id)
    {
        $company    =   \App\SellerCompany::find($id);
        $company_active = 'active';
        $our_sellers_active          =   'active';
        $location   =   [];
    
        $country    =   $this->locationIdRepository->IdLocation($company->country);
        $county    =   $this->locationIdRepository->IdLocation($company->county);
        $county_l4    =   $this->locationIdRepository->IdLocation($company->county_l4);
    
        if(isset($country)) $location['country']        =   $country;
        if(isset($county)) $location['county']          =   $county;
        if(isset($county_l4)) $location['county_l4']    =   $county_l4;
        
        $sales   =   DB::table('purchase_sales_statistics')
            ->where('seller_company_id',$id)
            ->pluck('order_value','department')
            ->toArray();
    
        return view('seller.about.seller',compact('company','company_active','our_sellers_active','location','sales'));
    }
    public function product_lists($id)
    {
        $product_lists    =   DB::table('product_lists')->where('buyer_company_id',$id)->pluck('product_list','department');
        $product_lists_active = 'active';
        $our_buyers_active          =   'active';
        $company    =   \App\BuyerCompany::find($id);
       
        return view('seller.about.product_lists',compact('company','product_lists_active','product_lists','our_buyers_active'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
