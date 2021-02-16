<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Events\BuyerNotificationEvent;
use App\Events\SellerNotificationEvent;
use App\Services\DeliveryDays;
use App\Services\Departments;
use App\Services\LocationNameOrId;
use App\Services\PaymentFrequency;
use App\Services\PriceList;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Repository\NavigationRepository;
use App\Repository\OrderingRepository;
use App\Services\Role;
use App\Repository\CountryRepository;
use Illuminate\Support\Facades\Auth;
use App\Jobs\CompanyActivationEmailJob;
use Illuminate\Support\Facades\Gate;
use App\Services\Company;



class DepartmentController extends Controller
{
    
    
    public $productListRepository;
    public $carbonLocale;
    public $company;
    public $role;
    public $departments;
    
    public function __construct(
                                 NavigationRepository $navigationRepository,
                                 OrderingRepository $orderingRepository,
                                 Role $role,
                                 Company $company,
                                 CountryRepository $countryRepository,
                                 Departments $departments
                               )
    {
       
        $this->middleware('buyer.auth:buyer')->except('toggle_buyer_seller');
        $this->middleware('buyer_seller')->only('toggle_buyer_seller');
        $this->navigationRepository     =   $navigationRepository;
        $this->orderingRepository       =   $orderingRepository;
        $this->role                     =   $role;
        $this->countryRepository        =   $countryRepository;
        $this->company                  =   $company;
        $this->departments              =   $departments;
        
    }
   
    private function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
    public function show($department)
    {
        $active_seller_id = session()->has('seller_updated_prices_id') ?
            session()->pull('seller_updated_prices_id') : null;
        
        $buyer_company_id = session()->get('company_id');
        $company                    =  $this->companies()[$buyer_company_id];
        $days = $this->role->days();
        $payment_frequency =PaymentFrequency::get();
        $seller_status =  isset($company->price_lists[$department]) ? $company->price_lists[$department] : [];
     
        return view('buyer.department.dashboard', compact('company','department','seller_status','days','payment_frequency','active_seller_id'));
    }
    public function search_sellers(Request $request)
    {
        if(! $company  =  $this->companies()[$request->company_id])  abort(401);
        
        $search_country = 'select' ;
        $active['Search_sellers']   =   'active';
        $departments =  $this->departments->for_bc($request->company_id);
        $creating_company_class ='';
        $country_levels = LocationNameOrId::current_countries_for_select();
        $county_levels =[];
        $county_levels_4 =[];
        $highier = true;
        $search_deeper =  [];
        $search =  [];
        $welcome  =    true;
        $searched_department    =   null;
        if($request->country == null )
        {
            return view('buyer.department.search_sellers', compact(
                'active',
                'creating_company_class',
                'country_levels',
                'county_levels',
                'county_levels_4',
                'departments',
                'company',
                'welcome',
                'searched_department'
            ));
        }
        $sellers_in_country = [
        
            'delivery_locations.department'    =>  str_replace(' ','_',$request->department),
            'delivery_locations.country'    =>  $request->country];
    
        $sellers_in_county = [
        
            'delivery_locations.department'    =>  str_replace(' ','_',$request->department),
            'delivery_locations.country'    =>  $request->country,
            'delivery_locations.county'     =>  $request->county];
    
        $sellers_in_county_l4 = [
        
            'delivery_locations.department'    =>  str_replace(' ','_',$request->department),
            'delivery_locations.country'    =>  $request->country,
            'delivery_locations.county'     =>  $request->county,
            'delivery_locations.county_l4'  =>  $request->county_l4];
    
       
        if($request->county_l4 != null)
        {
           
            $search =  $sellers_in_county_l4;
            
            $search_deeper = [
    
                'delivery_locations.department'    =>  str_replace(' ','_',$request->department),
                'delivery_locations.country'    =>  $request->country,
                'delivery_locations.county'     =>  null,
                'delivery_locations.county_l4'  =>  null];
        }
        elseif($request->county != null)
        {
           
            $search = $sellers_in_county;
    
            $search_deeper =    [ 'delivery_locations.department'    =>  str_replace(' ','_',$request->department),
                'delivery_locations.country'    =>  $request->country,
                'delivery_locations.county'     =>  null];
            
           
        }
        elseif($request->country != null)
        {
            $search =   $sellers_in_country;
            $search_deeper =  [];
            $highier = false;
        }
       
            $sellers=DB::table('delivery_locations')
                ->join('seller_companies','delivery_locations.seller_company_id','=','seller_companies.id')
                ->leftJoin('product_list_requests','product_list_requests.seller_company_id','=','seller_companies.id')
                ->where($search)
                ->get([ 'seller_company_name as name',
                    'seller_companies.id as id',
                    'seller_companies.address',
                    'delivery_locations.country',
                    'delivery_locations.county',
                    'delivery_locations.county_l4',
                    'delivery_locations.department as department',
                    'product_list_requests.requested',
                    'product_list_requests.responded',
                    'product_list_requests.guard as requester'])
                ->unique()
                ->sortBy(['requested'])
                ->toArray();
        
        if($highier){
            $highier_sellers=DB::table('delivery_locations')
                ->join('seller_companies','delivery_locations.seller_company_id','=','seller_companies.id')
                ->leftJoin('product_list_requests','product_list_requests.seller_company_id','=','seller_companies.id')
                ->where($search_deeper)
                ->get([ 'seller_company_name as name',
                    'seller_companies.id as id',
                    'seller_companies.address',
                    'delivery_locations.country',
                    'delivery_locations.county',
                    'delivery_locations.county_l4',
                    'delivery_locations.department as department',
                    'product_list_requests.requested',
                    'product_list_requests.responded',
                    'product_list_requests.guard as requester'])
                ->unique()
                ->sortBy(['requested'])
                ->toArray();
        }
        else{
            $highier_sellers    =   [];
        }
        
       
      
        $search_sellers = $this->group_companies(array_merge($sellers,$highier_sellers));
    
    
       
        
        $searched_department = $request->department;
        if($request->county !=  '')
        {
            $search_location[1] = $request->county;
        }
        else{
            $search_location[1] = '';
        }
        if($request->county_l4 !=  '')
        {
            $search_location[2] = $request->county_l4;
        }
        else{
            $search_location[2] ='';
        }
        $search_location[0] = $request->country;
        
        $search_location = LocationNameOrId::path($search_location);
        
        return view('buyer.department.search_sellers', compact(
            'active',
            'search_country',
            'search_sellers',
            'country_levels',
            'creating_company_class',
            'county_levels',
            'county_levels_4',
            'search_location',
            'searched_department',
            'departments',
            'company'
        ));
    }
    public function sellers(Request $request)
    {
    
        if(! $company  =  $this->companies()[$request->company_id])  abort(401);
     
        $pageName = __('Available Providers');
        
        $new_sellers =DB::table('seller_companies')->distinct()
            ->leftJoin('delivery_locations', function ($join) {
                $join->on('delivery_locations.seller_company_id', '=', 'seller_companies.id');
            })
            ->join('product_lists', function ($join) {
                $join->on('product_lists.department', '=', 'delivery_locations.department');
            })
            ->leftJoin('buyer_companies','buyer_companies.id','=','product_lists.buyer_company_id')
            ->leftJoin('product_list_requests', function ($join) {
                
                $join->on('product_list_requests.seller_company_id', '=', 'seller_companies.id');
                $join->on('product_list_requests.department', '=', 'product_lists.department');
                $join->on('product_list_requests.buyer_company_id', '=', 'buyer_companies.id');

            })

            ->leftJoin('price_lists', function ($join) {
                $join->on('price_lists.seller_company_id', '=', 'seller_companies.id');
                $join->on('price_lists.department', '=', 'product_lists.department');
                $join->on('price_lists.buyer_company_id', '=', 'buyer_companies.id');

            })
          
//                        ->leftJoin('buyer_companies', function ($join) {
//                $join->on('buyer_companies.id', '=', 'product_lists.buyer_company_id');
//            })
//
            
           
            ->where('delivery_locations.country',$company->country)
            ->where('buyer_companies.id',session()->get('company_id'))
            
            ->get([
                'buyer_companies.buyer_company_name',
                'buyer_companies.buyer_email',

                'buyer_companies.country as buyer_country',
                'buyer_companies.county  as buyer_county',
                'buyer_companies.county_l4  as buyer_county_l4',
                
                'seller_companies.seller_company_name as name',
                'seller_companies.seller_company_name as seller_company_name',
                'seller_companies.id as id',
                'buyer_companies.id as buyer_company_id',
                'seller_companies.seller_email',
                'seller_companies.address',
                'seller_companies.country',
                'seller_companies.county',
                'seller_companies.county_l4',
                
                'price_lists.department as price_lists_department',
                'product_list_requests.department as product_list_requests_department',
                'product_list_requests.requested',
                'product_list_requests.responded',
                'product_list_requests.guard as requester',
                'product_lists.department as department',
                'delivery_locations.id as delivery_location_id',
                'delivery_locations.department as delivery_location_department',
                'delivery_locations.country as delivery_location_country'
            ])
            ->unique()
            ->toArray();
        

        $sorted_sellers =   $this->sort_sellers($new_sellers);
       // dd($sorted_sellers,$new_sellers);
        $undiscovered_sellers['size']        =   sizeof($sorted_sellers['undiscovered']);
        $to_be_sellers['size']               =   sizeof($sorted_sellers['to_be']);
        $ready_sellers['size']               =   sizeof($sorted_sellers['ready']);
    
        $undiscovered_sellers['companies']    =   $this->group_companies($sorted_sellers['undiscovered']);
        $to_be_sellers['companies']           =   $this->group_companies($sorted_sellers['to_be']);
        $ready_sellers['companies']           =   $this->group_companies($sorted_sellers['ready']);
    
       
        if($request->county !=  '')
        {
            $search_location[1] = $company->county;
        }
        else{
            $search_location[1] = '';
        }
        if($request->county_l4 !=  '')
        {
            $search_location[2] = $company->county_l4;
        }
        else{
            $search_location[2] ='';
        }
        $search_location[0] = $company->country;
    
     
        $search_location = LocationNameOrId::path($search_location);
        
        $search_country = $company->country ;
        
        $searchedDepartment = $request->department;
    
        $departments =  $this->departments->for_bc($request->company_id);
    
        $country_levels = LocationNameOrId::current_countries_for_select();
        $county_levels =[];
        $county_levels_4 =[];
        $active['Sellers']   =   'active';
       
        return view('buyer.department.sellers', compact(
            'active',
            'search_country',
            'search_sellers',
            'undiscovered_sellers',
            'to_be_sellers',
            'ready_sellers',
            'sellers',
            'sellers_answered',
            'sellers_not_answered',
            'searchedDepartment',
            'pageName',
            'company',
            'departments',
            'country_levels',
            'county_levels',
            'county_levels_4',
            'search_location'
           ));
    }
    private function group_companies($companies )
    {
        return LocationNameOrId::group_companies($companies);
    }
    public function toggle_buyer_seller( Request $request)
    {
    
        if($request->url  ==  'deactivate_seller')
        {
            $update = ['activated_by_buyer'    =>   0 ];
    
            $move           =   __('deactivated');
        }
        if($request->url  ==  'activate_seller')
        {
    
            $update = ['activated_by_buyer'    =>   1 ];
    
            $move           =   __('activated');
        }
        if($request->url  ==  'deactivate_buyer')
        {
            $update = ['activated_by_seller'    =>   0 ];
    
            $move           =   __('deactivated');
        }
        if($request->url  ==  'activate_buyer')
        {
            $update = ['activated_by_seller'    =>  1 ];
            
            $move           =   __('activated');
        }
     
        if( DB::table('price_lists')
            ->where( 'buyer_company_id', $request->buyer_company_id)
            ->where( 'seller_company_id', $request->seller_company_id)
            ->where( 'department', $request->department)
            ->update($update))
        {
            $update = 'ok';
        }
        else{
            return ['status' =>'no price list', 'text'    =>  __('You do not have prices for the buyer yet !')]   ;
        }
        
        if( $update === 'ok')
        {
            if($request->url == 'activate_buyer' || $request->url == 'deactivate_buyer')
            {
                $seller_details   =   $this->companies()[$request->seller_company_id];
                $buyer_details   =    $this->companies()[$request->seller_company_id]->buyer_companies[$request->buyer_company_id];
               
               ////PUSHER NOTIFICATION
                $details=[
                            'n_link'                        =>   '/department/'.$request->department,
                            'action'                        =>  'company_activation',
                            'activator_company_name'        =>  $seller_details->seller_company_name,
                            'activator_company_id'          =>  $request->seller_company_id,
                            'other_company_name'            =>  $buyer_details['company_name'],
                            'other_company_owner_email'     =>  $buyer_details['buyer_owner_email'],
                            'buyer_company_id'              =>  $request->buyer_company_id,
                            'seller_company_id'             =>  $request->seller_company_id,
                            'move'                          =>  $move,
                            'activator'                     =>  'seller',
                            'department'                    =>  $request->department,
                            'subject'                       =>   __('Your company was moved by other_company',
                                                                        [
                                                                            'other_company'     =>  $seller_details->seller_company_name,
                                                                            __('moved')   =>  $move
                                                                        ]),
                
                ];
                //// dispatch email to buyer owner
                dispatch(new CompanyActivationEmailJob($details));
    
                /////// UPDATE PRICE LISTS
                $this->company->update('price_lists');
                
                /// PUSHER
                BuyerNotificationEvent::dispatch($details);
            }
            elseif($request->url == 'activate_seller' || $request->url == 'deactivate_seller')
            {
                $buyer_details   =   $this->companies()[$request->buyer_company_id];
                $seller_details   =  $this->companies()[$request->buyer_company_id]->seller_companies[$request->seller_company_id];
                
                ////PUSHER NOTIFICATION
                $details=[
                          'n_link'                        =>   '/pricing/'.$request->buyer_company_id.'/'.$request->department.'/'.$request->seller_company_id,
                          'action'                      =>  'company_activation',
                          'activator_company_name'      =>  $buyer_details->buyer_company_name,
                          'activator_company_id'        =>  $request->buyer_company_id,
                          'other_company_name'          =>  $seller_details['company_name'],
                          'other_company_owner_email'   =>  $seller_details['seller_owner_email'],
                          'seller_company_id'           =>  $request->seller_company_id,
                          'buyer_company_id'            =>  $request->buyer_company_id,
                          'move'                        =>  $move,
                          'activator'                   =>  'buyer',
                          'department'                  =>  $request->department,
                          'subject'                     => __('Your company was moved by other_company',
                                                              [
                                                                  'other_company' =>  $buyer_details->buyer_company_name,
                                                                  __('moved')         =>  $move
                                                              ]),
                    ];
                //// dispatch email to seller owner
   
                 dispatch(new CompanyActivationEmailJob($details));
               
                 /////// UPDATE PRICE LISTS
                 $this->company->update('price_lists');
               //// PUSHER
                SellerNotificationEvent::dispatch($details);
            }
            
            return ['status' =>'updated', 'text'    =>  __('Change made !')]   ;
        }
        else
        {
            return ['status' =>'no price list', 'text'    =>  __('You do not have prices for the buyer yet !')]   ;
           
        }
       
    }
    private function sort_sellers($seller_companies)
    {
        /*SORTING BY READY, TO BE , UNDISCOVERED BUYERS*/
        $sorted_seller_companies = [];
        $sorted_seller_companies['undiscovered'] = [];
        $sorted_seller_companies['to_be'] = [];
        $sorted_seller_companies['ready'] = [];
       
        foreach ($seller_companies as $seller_company)
        {
            if($seller_company->product_list_requests_department == null && $seller_company->price_lists_department == null && $seller_company->department != null)
            {
                $sorted_seller_companies['undiscovered'][]   =   $seller_company;
            }
            if($seller_company->product_list_requests_department != null && $seller_company->price_lists_department == null)
            {
                $sorted_seller_companies['to_be'][]   =   $seller_company;
            }
            if($seller_company->product_list_requests_department != null && $seller_company->price_lists_department != null)
            {
                $sorted_seller_companies['ready'][]   =   $seller_company;
            }
        }
        
        return $sorted_seller_companies;
    }
}
