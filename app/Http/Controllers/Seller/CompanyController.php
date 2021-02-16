<?php

namespace App\Http\Controllers\Seller;

use App\Delegation;

use App\Events\SellerNotificationEvent;
use App\Services\Departments;
use App\Services\ExtendedPriceList;
use App\Services\Language;
use App\Services\LocationNameOrId;
use App\Services\PaymentFrequency;
use App\Services\Strings;
use App\Services\StrReplace;
use Illuminate\Http\Request;
use DB;
use App\SellerCompany;
use App\BuyerCompany;
use App\Http\Requests\SellerCompanyRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\Role;
use App\Repository\DelegationRepository;
use App\Seller;
use Illuminate\Support\Facades\Gate;
use App\Services\Currency;
USE App\Services\Company;
use Illuminate\Support\Facades\Log;


class CompanyController extends Controller
{
    public $currency;
    public $role;
    public $company;
    public $departments;
    public $delegator;
    
    public function __construct(
                                 DelegationRepository $delegator,
                                 Role $role,
                                 Currency $currency,
                                 Company $company,
                                 Departments $departments)
    {
        $this->middleware('seller.auth:seller');
        $this->delegator    =   $delegator;
        $this->role         =   $role;
        $this->currency     =   $currency;
        $this->company      =   $company;
        $this->departments  =   $departments;
    }
    
    private function seller_companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
    
    public function index($company_id)
    {
      
       // $this->company->update(['companies','staff']);
        $companies                =   $this->seller_companies();
        $company                  =   $companies[$company_id];

        session(['company_id' => $company_id]);
        session(['seller_company_name' => $company->seller_company_name]);
        session(['company_name' => $company->seller_company_name]);
        session()->put('company_languages', $company->languages['all'] );
        session()->put('company_preferred_lan', $company->languages['preferred'] );

        if(\Auth::guard('seller')->user()->role  ==  'seller_owner')
        {
            return view('seller.company.dashboard', compact('company'));
        }
    
        if(\Auth::guard('seller')->user()->role  ==  'seller_seller'
            || Auth::guard('seller')->user()->role  ==  'seller_accountant'
            || Auth::guard('seller')->user()->role  ==  'seller_delivery')
        {
    /*STAFF LOGS INTO THE COMPANY PAGE AFTER CLICKING ON ACCEPT THE JOB IN THIS COMPANY
    WE WILL UPDATE DELEGATION AS ACCEPTED*/
    
            if($company->logged_in_staff['accepted_at']  == null
                && $company->logged_in_staff['staff_email'] == Auth::guard('seller')->user()->email
                && $company->logged_in_staff['company_id'] == $company_id)
           {
               DB::table('delegations')
                   ->where('staff_email',\Auth::guard('seller')->user()->email)
                   ->where('delegator_company_id',$company_id)
                   ->where('staff_role',\Auth::guard('seller')->user()->role)
                   ->update([
                       'staff_id'      =>  \Auth::guard('seller')->user()->id,
                       'accepted_at'  => date('Y-m-d H:i:s')]);
             
          
    
            $details=[
                'n_link'                         =>   '/staff',
                'action'                         =>   'staff_accepted_job',
                'guard'                          =>   'seller',
                'owner_email'                    =>    $company->seller_owner_email,
                'subject'            =>  __('staff_name accepted position of staff_role role.',
                    [
                        'staff_name'     =>   Auth::guard('seller')->user()->name,
                        'position'       =>   $company->logged_in_staff['staff_position'],
                        'staff_role'     =>   Auth::guard('seller')->user()->role
                    ]),
            ];
            /////    PUSHER
            SellerNotificationEvent::dispatch($details);
    
            $this->company->update(['staff','buyer_companies']);
    
               $companies                =   $this->seller_companies();
               $company                  =   $companies[$company_id];
           }
            return view('seller.staff_includes.company', compact('company','companies'));
        }
       
    }
    public function create()
    {
        session()->put('creating_company',true);
        
        $creating_company_class     =   'd-none';
        $country_levels             =   LocationNameOrId::current_countries_for_select();
        $county_levels              =   [];
        $county_levels_4            =   [];
        $payment_frequency          =   PaymentFrequency::get();
        $info = (object)    [
            'days'  =>  $this->role->days()
        ];
        $location_path              =   '';
        
        session()->pull('selected_country') ;
        session()->pull('company_currencies');
        session()->pull('company_languages');
        
        $times = array();
        
        for ($h = 0; $h < 24; $h++){
            for ($m = 0; $m < 60 ; $m += 5){
                $time = sprintf('%02d:%02d', $h, $m);
                $times["$time"] = "$time";
            }
        
        }
       
        return view('seller.company.create', compact('country_levels',
            'county_levels',
            'county_levels_4',
            'times',
            'info',
            'payment_frequency',
            'location_path',
        'creating_company_class'
        ));
    }
    public function store(SellerCompanyRequest $request)
    {
      
        $country_currency   =   $this->currency ->get_country_currency($request->country);
        $preferred_currency =   $request->preferred_currency;
        $seller_company_id  =   $this->createCompany($request)->id;
       
        /*IF SELLER HAS SOME COMPANIES, WE WILL UPDATE SET*/
        if(SellerCompany::where('seller_id',Auth::guard('seller')->user()->id)->first())
        {
            $this->company->update(['staff',
                'seller_companies',
                'price_lists',
                'buyer_companies',
                'delivery_locations',
                'price_lists_extended',
                'product_lists']);
        }
      
        
        $this->check_staff($request);
    
        if($request->seller_owner_email != $request->seller_email){
           $this->delegator->delegate_staff($request,$seller_company_id,'seller_seller',$request->seller_email,'manager');
        }
        if($request->seller_owner_email != $request->seller_accountant_email){
       $this->delegator->delegate_staff($request,$seller_company_id,'seller_accountant',$request->seller_accountant_email,'manager');
        }
        if($request->seller_owner_email != $request->seller_delivery_email){
            $this->delegator->delegate_staff($request,$seller_company_id,'seller_delivery',$request->seller_delivery_email,'manager');
        }
        if($country_currency !==  $preferred_currency)
        {
        
            session()->push('seller_company_ids', $seller_company_id);
        
            return redirect('/edit_seller_company/'.$seller_company_id)
                ->withInput()
                ->with('form_updated',1)
                ->with( 'preferred_currency',$preferred_currency)
                ->with('country_currency',__('country_currency Currency from the country you are in seems to be not selected',
                    [
                        __('country_currency')   =>  $country_currency
                    ]));
        }
        return redirect('/seller');
    }
    public function show($id)
    {
    
    }
    public function edit($seller_company_id)
    {
        session()->pull('creating_company');
        session()->pull('selected_country') ;
        
        $creating_company_class     =   '';
        $county_levels              =   [];
        $county_levels_4            =   [];
        $country_levels             =   LocationNameOrId::current_countries_for_select();
        $company                    =   $this->seller_companies()[$seller_company_id];
        $payment_frequency          =   PaymentFrequency::get();
        $company_currencies         =   Currency::add_data_to_currency($company->currencies['all']);
        $country_currency           =   Currency::get_country_currency($company->country);
        $missing_country_currency   =   in_array($country_currency,$company->currencies['all']) ? false:  Currency::add_data_to_currency([$country_currency]) ;
        $preferred_currency         =   Currency::add_data_to_currency([$company->currencies['preferred']]);
        $location_path              =   LocationNameOrId::path([$company->country,$company->county,$company->county_l4]);
        $languages                  =   Language::get_language_names($company->languages);
        $country_languages          =   Language::country_language($company->country);
        $missing_country_languages  =   array_diff($country_languages,$languages['all']);
        $info = (object)    [
            'days'  =>  $this->role->days()
        ];
      
        session()->put('company_currencies', $company->currencies['all'] );
        session()->put('company_languages', $company->languages['all'] );
        
        $times = array();
        for ($h = 0; $h < 24; $h++){
            for ($m = 0; $m < 60 ; $m += 5){
                $time = sprintf('%02d:%02d', $h, $m);
                $times["$time"] = "$time";
            }
        }
       
    
        return view('seller.company.edit', compact(
            'times',
            'info',
            'company',
            'country_levels',
            'county_levels',
            'county_levels_4',
            'payment_frequency',
            'company_currencies',
            'preferred_currency',
            'location_path',
            'creating_company_class',
            'languages',
            'missing_country_languages',
            'missing_country_currency'));
    }
    public function update(SellerCompanyRequest $request, $seller_company_id)
    {
      
        if($request->preferred_currency   === null || $request->currencies === null)
        {
            return back()->withInput($request->request->all())->with('check','not');
        }
    
        $country_currency   =  $this->currency ->get_country_currency($request->country);
       
        $currency['all'] = $request->currencies;
        
        if($this->check_staff($request) ==  'seller_not_checked')
        {
            if($request->seller_owner_email != $request->seller_email)
            return back()->with('seller_not_checked',__('We have ')
                .Seller::where('email',$request->seller_email)->pluck('role')->first().__(' with this email'));
        }
        if($this->check_staff($request) ==  'seller_accountant_not_checked')
        {
            if($request->seller_owner_email != $request->seller_accountant_email)
            return back()->with('seller_accountant_not_checked',__('We have ')
                .Seller::where('email',$request->seller_email)->pluck('role')->first().__(' with this email'));
        }
      
        $preferred_language = $request->preferred_language;
        $preferred_currency = $request->preferred_currency;
        
        $company_details = $this->prepare_request( $request);
        
        $seller_company =  $this->seller_companies()[$seller_company_id];
   
        
        //$old_languages =  $seller_company->languages ;
        $old_preferred_language =   $seller_company->languages['preferred'];
        $old_all_languages =   $seller_company->languages['all'];
    
        //$old_currencies =  json_decode($seller_company->currencies,true) ;
        $old_preferred_currency =   $seller_company->currencies['preferred'];
        $old_all_currencies =   $seller_company->currencies['all'];
    
   // dd($seller_company,$company_details);
    
        if($old_preferred_language  !== $preferred_language)
        {
          
            ExtendedPriceList::change_preferred_language($old_preferred_language,$preferred_language,$old_all_languages,$this->role->get_owner_id());
        }
    
        if($old_preferred_currency  !== $preferred_currency)
        {
           ExtendedPriceList::change_preferred_currency($old_preferred_currency,$preferred_currency,$old_all_currencies);
        }
          /*EXTENDED PRICE LIST  REMOVED LANGUAGES*/
        if(!empty( array_diff($old_all_languages,
            json_decode($company_details['languages'],true)['all'])))
        {
            ExtendedPriceList::disable_language( array_diff($old_all_languages,
                json_decode($company_details['languages'],true)['all']));
        }
      
    
        /*EXTENDED PRICE LIST  REMOVED CURRENCIES*/
        if(!empty(array_diff($old_all_currencies,
            json_decode($company_details['currencies'],true)['all'])))
        {
            ExtendedPriceList::disable_currency(array_diff($old_all_currencies,
                json_decode($company_details['currencies'],true)['all']));
        }
        
        if($request->seller_email != $seller_company->seller_email  && $request->seller_email != $seller_company->seller_owner_email)
        {
            
            //// NEW REPO
           $this->delegator->delegate_staff($request,$seller_company_id,'seller_seller',$request->seller_email,'manager');
    
           $this->delegator->undelegate_staff($request,$seller_company->seller_email,'seller_seller','manager');
            
        }
        
        if($request->seller_accountant_email != $seller_company->seller_accountant_email && $request->seller_accountant_email != $seller_company->seller_owner_email)
        {
    
            ///// NEW REPO
           $this->delegator->delegate_staff($request,$seller_company_id,'seller_accountant',$request->seller_accountant_email,'manager');
    
           $this->delegator->undelegate_staff($request,$seller_company->seller_accountant_email,'seller_accountant','manager');
            
            
        }
    
        if($request->seller_delivery_email != $seller_company->seller_delivery_email && $request->seller_delivery_email != $seller_company->seller_owner_email)
        {
           
            ///// NEW REPO
           $this->delegator->delegate_staff($request,$seller_company_id,'seller_delivery',$request->seller_delivery_email,'manager');
        
           $this->delegator->undelegate_staff($request,$seller_company->seller_delivery_email,'seller_delivery','manager');
        
        
        }
        
        unset($company_details['_token']);
        unset($company_details['_method']);
      
        SellerCompany::where('id','=',$seller_company_id)->where('seller_id',\Auth::guard('seller')->user()->id)->update($company_details);
       
        
        DB::table('delegations')->where('delegator_company_id',$seller_company_id)->where('delegator_role','seller_owner')
                ->update(['delegator_company_name'  =>    $company_details['seller_company_name']]);
    
        $this->company->update(['staff','seller_companies']);
        
        if(!in_array($country_currency, $currency['all']))
        {
            return back()
                ->with('form_updated',1)
                ->with('country_currency',__('country_currency Currency from the country you are in seems not to be selected as your preferred currency',
                    [
                        __('country_currency')   =>  $country_currency
                    ]));
        }
        
        return back()->with('form_updated',1);
    }
    public function destroy($id)
    {
        //
    }
    public function buyers_for_accountant($seller_company_id)
    {
        /*ID'S OF BUYER COMPANIES IN ACCOUNTANT'S SCOPE OF WORK */
        $c_ids = $this->seller_companies()[$seller_company_id]->logged_in_staff['scope']['companies'];
      
        $buyer_companies = $this->seller_companies()[$seller_company_id]->buyer_companies;
        $price_lists = $this->seller_companies()[$seller_company_id]->price_lists;
        $days = $this->role->days();
        $frequencies    =   Strings::payment_frequency();
        $buyers = [];
        foreach($price_lists as $bc_id => $price_list)
        {
            if(in_array($bc_id,$c_ids))
            {
                $buyers[$bc_id]['price_list'] = $price_list;
                $buyers[$bc_id]['company'] = $buyer_companies[$bc_id];
            }
            
        }

       
        return view('seller.company.buyers_for_accountant',compact('buyers','frequencies','days'));
    }
    public function buyers(Request $request,$seller_company_id)
    {
        
        $outside_buyers = [];
        $searched_department ='';
        $company =$this->seller_companies()[$seller_company_id];
        $searched_location  =   '';
        //// REFINING BUYERS=> SEARCHING FOR NEW BUYERS
        if( $request->country)
        {
    
       
            $searched_department = str_replace(' ','_',$request->department);
           
            if($request->county_l4 != '')
            {
                $buyer_ids = $this->buyers_in_location('county_l4',$request->county_l4);
            }
            
            if($request->county != '' && !isset($request->county_l4) || $request->county != '' &&  $request->county_l4 != '')
            {
                $buyer_ids = $this->buyers_in_location('county',$request->county);
            }
            
            if( ($request->country != '' && !isset($request->county) ) || ($request->country != '' &&  $request->county != '' ) )
            {
                $buyer_ids = $this->buyers_in_location('country',$request->country);
            }
          
            $searched_location = LocationNameOrId::path($request->all());
          
            
        
            ///// WE HAVE BUYERS IN LOCATION, NOW WE NEED TO FIND OUT IF THEY HAVE DEPARTMENT SELLERS CAN SELL TO
            $outside_buyers =  DB::table('buyer_companies')->distinct()
                ->join('product_lists', 'buyer_companies.id', '=', 'product_lists.buyer_company_id')
                ->leftJoin('product_list_requests','product_list_requests.buyer_company_id','=','buyer_companies.id')
                ->whereIn('product_lists.buyer_company_id',$buyer_ids)
                ->where('product_lists.department',$searched_department)
                ->where('product_list_requests.buyer_company_id','=',null)
                ->get(['buyer_companies.id as buyer_company_id',
                    'buyer_companies.buyer_company_name'
                   ])
                ->toArray();
            
        
        }
        //// END REFINING BUYERS => SEARCHING FOR NEW BUYERS
    
        $country_levels = LocationNameOrId::current_countries_for_select();
        
        $county_levels =[];
        $county_levels_4 =[];
      
     
        $buyers=DB::table('buyer_companies')->distinct()
            ->leftJoin('product_lists', function ($join) {
                $join->on('product_lists.buyer_company_id', '=', 'buyer_companies.id');
            })
            ->leftJoin('delivery_locations', function ($join) {
                $join->on('delivery_locations.country', '=', 'buyer_companies.country');
                $join->on('product_lists.department', '=', 'delivery_locations.department');
            })
            ->join('seller_companies','seller_companies.id','=','delivery_locations.seller_company_id')
            ->leftJoin('product_list_requests', function ($join) {
              $join->on('product_list_requests.buyer_company_id', '=', 'buyer_companies.id');
            $join->on('product_list_requests.seller_company_id', '=', 'seller_companies.id');
             $join->on('product_list_requests.department', '=', 'product_lists.department');
                /*here make seller_id in product_list_requests and then
               */
               $join->on('product_list_requests.delivery_location_id', '=', 'delivery_locations.id');
                // $join->on('product_list_requests.seller_id', '=', 'sellers.id');
                // $join->on('product_list_requests.requester_user_id', '=', 'sellers.id');
            })
           
            ->leftJoin('price_lists', function ($join) {
                $join->on('price_lists.seller_company_id', '=', 'delivery_locations.seller_company_id');
                $join->on('price_lists.department', '=', 'product_list_requests.department');
                $join->on('price_lists.buyer_company_id', '=', 'buyer_companies.id');
        
            })
             ->leftjoin('sellers','sellers.id','=','product_list_requests.seller_id')
            //->leftjoin('sellers','sellers.id','=','price_lists.seller_id')
            ->where('delivery_locations.seller_company_id',$seller_company_id)
           // ->where('seller_companies.id',$seller_company_id)
          //->where('product_list_requests.seller_company_id',$seller_company_id)
           
        
            ->get([
                'buyer_company_name as name',
                'buyer_companies.id',
                'buyer_companies.address',
                'buyer_companies.country',
                'buyer_companies.county',
                'buyer_companies.county_l4',
                'buyer_companies.buyer_email',
               'buyer_companies.buyer_company_name',
              
    
               'product_lists.department as department',
    
                'delivery_locations.id as delivery_location_id',
                'delivery_locations.department as delivery_location_department',
                
               'product_list_requests.seller_company_id',
               'product_list_requests.department as product_list_requests_department',
               'product_list_requests.requested',
               'product_list_requests.responded',
               'product_list_requests.guard as requester',
               'product_list_requests.seller_id as requester_user_id',
              
            

               'price_lists.department as price_lists_department',
               'price_lists.seller_id as price_list_seller_id',


                'seller_companies.seller_email',
                'seller_companies.seller_company_name',
                
                'sellers.name as seller_name',
                'sellers.id as seller_id'
            ]);
          
     
       
        
        $sorted_buyers                      =   $this->sort_buyers($buyers);
       //dd($sorted_buyers);
        $undiscovered_buyers['size']        =   sizeof($sorted_buyers['undiscovered']);
        $to_be_buyers['size']               =   sizeof($sorted_buyers['to_be']);
        $ready_buyers['size']               =   sizeof($sorted_buyers['ready']);
        
        $undiscovered_buyers['companies']    =   $this->group_buyers($sorted_buyers['undiscovered']);
        $to_be_buyers['companies']           =   $this->group_buyers($sorted_buyers['to_be']);
        $ready_buyers['companies']           =   $this->group_buyers($sorted_buyers['ready']);
      // dd($to_be_buyers);
        $pageName = __('Buyers in your delivery locations :');
   
        $departments =  $this->departments->for_sc($seller_company_id);
        
        return view('seller.company.buyers' ,
            compact(
                'departments',
                'outside_buyers',
                'searched_department',
                'seller_company_id',
                'country_levels',
                'county_levels',
                'county_levels_4',
                'company',
                'pageName',
                'ready_buyers',
                'to_be_buyers',
                'undiscovered_buyers',
                'searched_location'
            ));
    }
    
    private function createCompany($request)
    {
        
        $company_details = $this->prepare_request( $request);
      
        $Company =  Auth::guard('seller')->user()->seller_companies()->create($company_details);
        
        return $Company;
        
    }
    private function prepare_request( $request)
    {
    
        ////// PUT LANGUAGES TOGETHER INTO JSON
        $currency['preferred'] = $request->preferred_currency;
        $currency['all'] = $request->currencies;
        $request->request->remove('preferred_currency');
        $request->request->remove('currencies');
        $request->request->add(['currencies'=>json_encode($currency)]);
        
       ////// PUT LANGUAGES TOGETHER INTO JSON
        $languages['preferred'] = $request->preferred_language;
        $languages['all'] = $request->languages;
        $request->request->remove('preferred_language');
        $request->request->remove('languages');
        $request->request->add(['languages'=>json_encode($languages)]);
        
        $company_details = $request->all();
        
        $company_details['address']         = json_encode($company_details['address']);
        $company_details['delivery_days']   = json_encode($company_details['delivery_days']);
        
      
        
        return $company_details;
    }
    private function check_staff($request)
    {
        if(Seller::where('email',$request->seller_email)->where('role','!=','seller_seller')->first())
        {
            if(\Auth::guard('seller')->user()->email !== $request->seller_email)
                return 'seller_not_checked';
            // return back()->with('seller_not_checked','We have '.User::where('email',$request->seller_email)->pluck('role')->first().' with this email');
            
        }
        elseif(Seller::where('email',$request->seller_accountant_email)->where('role','!=','seller_accountant')->first())
        {
            if(\Auth::guard('seller')->user()->email !== $request->seller_accountant_email)
                return 'seller_accountant_not_checked';
            //return back()->with('seller_accountant_not_checked','We have '.User::where('email',$request->seller_accountant_email)->pluck('role')->first().' with this email');
            
        }
        
    }
    private function group_buyers($companies )
    {
        return LocationNameOrId::group_companies($companies);
    }
    private function buyers_in_location($level, $level_id)
    {
        $buyer_ids =  DB::table('buyer_companies')
            ->where($level,'=',$level_id)
            ->pluck('id')->toArray();
        
        return $buyer_ids;
    }
    private function sort_buyers($buyer_companies)
    {
        /*SORTING BY READY, TO BE , UNDISCOVERED BUYERS*/
        $sorted_buyer_companies = [];
        $sorted_buyer_companies['undiscovered'] = [];
        $sorted_buyer_companies['to_be'] = [];
        $sorted_buyer_companies['ready'] = [];
       
        foreach ($buyer_companies as $buyer_company)
        {
            if($buyer_company->requester == 'seller' &&
                $buyer_company->seller_id == $buyer_company->requester_user_id)
            {
//                if($buyer_company->product_list_requests_department == null && $buyer_company->price_lists_department == null && $buyer_company->department != null)
//                {
//                    $sorted_buyer_companies['undiscovered'][]   =   $buyer_company;
//                }
                if($buyer_company->product_list_requests_department != null && $buyer_company->price_lists_department == null)
                {
                    $sorted_buyer_companies['to_be'][]   =   $buyer_company;
                }
                if($buyer_company->product_list_requests_department != null && $buyer_company->price_lists_department != null)
                {
                    $sorted_buyer_companies['ready'][]   =   $buyer_company;
                }
            }
            elseif($buyer_company->requester == 'buyer')
            {
//                if($buyer_company->product_list_requests_department == null && $buyer_company->price_lists_department == null && $buyer_company->department != null)
//                {
//                    $sorted_buyer_companies['undiscovered'][]   =   $buyer_company;
//                }
                if($buyer_company->product_list_requests_department != null && $buyer_company->price_lists_department == null)
                {
                    $sorted_buyer_companies['to_be'][]   =   $buyer_company;
                }
                if($buyer_company->product_list_requests_department != null && $buyer_company->price_lists_department != null)
                {
                    $sorted_buyer_companies['ready'][]   =   $buyer_company;
                }
            }
            elseif($buyer_company->requester == null)
            {
                $sorted_buyer_companies['undiscovered'][]   =   $buyer_company;
            }
            
            
        }
      
        return $sorted_buyer_companies;
    }
}
