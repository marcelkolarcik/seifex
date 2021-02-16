<?php

namespace App\Http\Controllers\Buyer;

use App\Services\Strings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Repository\NavigationRepository;
use App\Repository\LocationIdRepository;

class AboutController extends Controller
{
    
    public $strings;
    public $navigationRepository;
    public $locationIdRepository;
    public function __construct(  NavigationRepository $navigationRepository,LocationIdRepository $locationIdRepository, Strings $strings )
    {
        $this->navigationRepository = $navigationRepository;
        $this->locationIdRepository = $locationIdRepository;
        $this->strings = $strings;
        $this->middleware('buyer.auth:buyer');
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
    
        $companies                  =  $this->companies();
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
            ->where('buyer_company_id',array_keys($companies))
            ->get(['order_value','department','buyer_company_id'])
            //->groupBy(['seller_company_id','department'])
            ->toArray() ;
        $all_sales      =   [];
        $order_value    =   0;
        $buyer_company_ids =   [];
        $buyer_company_departments=   [];
        /*https://www.redtube.com/13047531*/
        foreach($sales as $sale)
        {
            if(isset($buyer_company_ids[sizeof($buyer_company_ids) - 1]))
            {
                if($buyer_company_ids[sizeof($buyer_company_ids) - 1] != $sale->buyer_company_id)
                {
                    $order_value =   0;
                }
                if($buyer_company_departments[$sale->seller_company_id][sizeof($buyer_company_departments[$sale->buyer_company_id]) - 1] != $sale->department)
                {
                    $order_value =   0;
                }
            }
        
            $order_value += $sale->order_value;
            $seller_company_ids[]    =  $sale->buyer_company_id;
            $buyer_company_departments[$sale->buyer_company_id][]    =  $sale->department;
            $all_sales[$sale->buyer_company_id][$sale->department] = $order_value ;
        
        
        
        
        }
        // dd($all_sales);
        return view('buyer.about.index',compact('companies','location','all_sales'));
    
    
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function product_lists()
    {
        $companies      =  $this->companies();
        $product_lists         =   DB::table('product_lists')->whereIn('buyer_company_id',array_keys($companies))->pluck('product_list','department');
        $product_lists_active  =   'active';
       // dd($prices);
        return view('buyer.about.product_lists',compact('product_lists','product_lists_active'));
    }
    public function our_sellers()
    {
        $companies                  =  $this->companies();
        
        $our_sellers   =   DB::table('purchase_sales_statistics')
            ->join('seller_companies', 'purchase_sales_statistics.seller_company_id', '=', 'seller_companies.id')
            ->where('buyer_company_id',array_keys($companies))
            ->get(['department','seller_company_id','order_value','seller_companies.seller_company_name'])
            ->sortByDesc('order_value')
            ->groupBy('department')
            ->toArray();
    
       
        $our_sellers_active          =   'active';
        
        return view('buyer.about.our_sellers',compact('our_sellers','our_sellers_active','orders'));
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
    
    ///// SHOW SELLERS
    public function show($id)
    {
        $companies                  =  $this->companies();
      
        $seller_company                    =   \App\SellerCompany::find($id);
    
        $delivery_days_  = DB::table('buyer_delivery_days')
            ->whereIn('buyer_company_id',array_keys($companies))
            ->where('seller_company_id',$id)
            ->get(['delivery_days','department'])
            ->toArray();
        
      //  dd($delivery_days);
        
        $days_=[];
    if(  $delivery_days_ === [] )
    {
        $delivery_days['default']  = json_decode( $seller_company->delivery_days,true) ;
    }
    else{
        foreach($delivery_days_ as $key   => $data)
        {
           foreach(json_decode($data->delivery_days,true) as $day)
           {
               $delivery_days[$data->department][] =   $this->strings->days()[$day];
           }
        }
    }
       // dd($delivery_days);
   /* else
    {
        $delivery_days  =   json_decode( $delivery_days->delivery_days,true) ;
    }*/
       // dd($delivery_days );
     ///// TO DO THE SAME FOR INVOICE FREQUENCY
     
        $invoice_frequency_for_buyer    =   DB::table('invoice_frequency')
            ->where('buyer_company_id',session()->get('company_id'))
            ->where('seller_company_id',$id)
            ->get(['department','invoice_frequency'])
            ->toArray();
        
      //  dd($seller_company->payment_method,$invoice_frequency_for_buyer,$companies);
        $frequencies=[];
     foreach ( $invoice_frequency_for_buyer as $frequency)
     {
         $frequencies[$frequency->department]   =  $this->strings->payment_frequency()[ $frequency->invoice_frequency ];
     }
     
     
    if($frequencies === [])
    {
        $frequencies['default'] =  $this->strings->payment_frequency()[ $seller_company->payment_method ] ;
    }
    
       
  
    
      
        $company_active = 'active';
        $our_sellers_active          =   'active';
        $location   =   [];
    
        $country      =   $this->locationIdRepository->IdLocation($seller_company->country);
        $county       =   $this->locationIdRepository->IdLocation($seller_company->county);
        $county_l4    =   $this->locationIdRepository->IdLocation($seller_company->county_l4);
    
        if(isset($country)) $location['country']        =   $country;
        if(isset($county)) $location['county']          =   $county;
        if(isset($county_l4)) $location['county_l4']    =   $county_l4;
        
       
        
        $sales   =   DB::table('purchase_sales_statistics')
            ->where('seller_company_id',$id)
            ->where('buyer_company_id',session()->get('company_id'))
            ->pluck('order_value','department')
            ->toArray();
        
        $their_buyers   =   DB::table('buyer_companies')
            ->whereIn('id',array_keys($companies))
            ->pluck('buyer_company_name','id');
        
        
        return view('buyer.about.seller',compact('seller_company',
            'company_active',
            'our_sellers_active',
            'location',
            'sales',
            'their_buyers',
            'delivery_days',
            'frequencies'));
    }
    public function show_buyer($id)
    {
        $company    =   \App\BuyerCompany::find($id);
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
            
            ->pluck('order_value','department')
            ->toArray();
        
        return view('buyer.about.buyer',compact('company','company_active','our_buyers_active','location','sales'));
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
