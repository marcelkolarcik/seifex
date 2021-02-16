<?php
namespace App\Repository;

use App\Events\BuyerNotificationEvent;
use App\Events\SellerNotificationEvent;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\BuyerCompany;
use App\SellerCompany;
use App\Jobs\InvoiceEmailJob;
use App\Services\Role;
use App\Services\Company;

class InvoiceRepository{
    
    public $role;
    public $company;
    
    public function __construct(Role $role, Company $company)
    {
       $this->role      = $role;
       $this->company   = $company;
    }
    private function companies  ()
    {
      return  $this->company->for($this->role->get_owner_or_staff());
    }
    public function create_invoices( $invoice_frequency   =   null, $invoice_type   =   null)
    {
        $owner  =   $this->role->get_guard();
        $logged_in_staff = $this->companies()[session()->get('company_id')]->logged_in_staff;
        
        $all_orders = DB::table('orders')
           ->where($owner.'_company_id',session()->get('company_id'))
            ->get();
        
   
        if($owner   ==  'buyer' )
        {
           // $opp_company_id        =   'seller_company_id';
            /*IF STAFF == BUYER ACCOUNTANT GET COMPANIES BY IDS OF COMPANIES HE IS WORKING WITH      ->whereIn('seller_company_id',['3']) ...  */
            if($logged_in_staff  &&  $logged_in_staff['role'] == 'buyer_accountant'){
                $staff_works_with      =   $logged_in_staff['scope']['companies'];
                $all_orders =   $all_orders->whereIn('seller_company_id',$staff_works_with);
            }
            if($logged_in_staff  &&  $logged_in_staff['role'] == 'buyer_buyer'){
                $departments      =   $logged_in_staff['scope']['departments'];
                $all_orders =   $all_orders->whereIn('department',$departments);
            }
            
        // dd($all_orders);
            $company_ids                =   array_keys($this->companies());
            $company_id_name            =   'buyer_company_id';
            $group_by_company_name      =   'seller_company_name';
            $opposite_owner             =   'seller';
            
        }
        elseif( $owner   ==  'seller' )
        {
           // $opp_company_id        =   'buyer_company_id';
           
            if($logged_in_staff  &&   in_array($logged_in_staff['role'],['seller_accountant']) ){
                
                $logged_in_staff['scope']['companies'] == null ?:
                $all_orders =   $all_orders->whereIn('buyer_company_id',$logged_in_staff['scope']['companies']);
            }
            if($logged_in_staff  &&   in_array($logged_in_staff['role'],['seller_seller','seller_delivery']) ){
                
                $logged_in_staff['scope']['departments'] == null ?
                    :   $all_orders =  $all_orders->whereIn('department',$logged_in_staff['scope']['departments']);
    
               
                
                /*LOCATIONS IDS*/
                !isset($logged_in_staff['scope']['base_locations']['country']) ?
                    : $locations_staff_works_with[]['country'] = array_keys($logged_in_staff['scope']['base_locations']['country']) ;
    
                !isset($logged_in_staff['scope']['base_locations']['county']) ?
                    : $locations_staff_works_with[]['county'] = array_keys($logged_in_staff['scope']['base_locations']['county']) ;
    
                !isset($logged_in_staff['scope']['base_locations']['county_l4']) ?
                    : $locations_staff_works_with[]['county_l4'] = array_keys($logged_in_staff['scope']['base_locations']['county_l4']) ;
    
                $staff_works_with=DB::table('buyer_companies');
    
                foreach($locations_staff_works_with as $key => $wheres)
                {
                    foreach($wheres as $level   => $ids)
                    {
                        if($key ==  0)
                        {
                            $staff_works_with->whereIn($level, $ids);
                        }
                        else
                        {
                            $staff_works_with->orWhereIn($level, $ids);
                        }
                    }
                }
               
                $staff_works_with      =    $staff_works_with->pluck('id')->toArray();
    
                $all_orders =   $all_orders->whereIn('buyer_company_id',$staff_works_with);
            
              
            }
            
    
            $company_ids                =   array_keys($this->companies());
            $company_id_name            =   'seller_company_id';
            $group_by_company_name      =   'buyer_company_name';
            $opposite_owner             =   'buyer';
        }
       
        $daily_invoices                 =   [];
        $weekly_invoices                =   [];
        $monthly_invoices               =   [];
        $daily_total           =   [];
        $weekly_total          =   [];
        $monthly_total         =   [];
        $company_orders_total           =   0;
        $company_daily_invoices_total   =   [];
        $company_weekly_invoices_total  =   [];
        $company_monthly_invoices_total =   [];
    
        $company_daily_order_ids        =   [];
        $company_weekly_order_ids       =   [];
        $company_monthly_order_ids      =   [];
        $invoice_frequency_names        =   [
            1  =>  'daily_invoices',
            2  =>  'weekly_invoices',
            3  =>  'monthly_invoices'
        ];
        
        if(isset($invoice_frequency) && isset($invoice_type))
        {
            /*  HERE YOU NEED DAILY,WEEKLY,MONTHLY INVOICES ACCORDING TO $invoice_frequency [1,2,3]*/
            $frequency_names =   $invoice_frequency_names[$invoice_frequency];
            
            if($invoice_type == 'unpaid_invoices')
            {
                
                $$frequency_names  =   $all_orders->whereIn($company_id_name,$company_ids)
                    ->where('invoice_freq',$invoice_frequency)
                    /*HERE IF STAFF IS ACCOUNTANT */
                  /*  ->whereIn($opp_company_id,$staff_works_with)*/
                    ->where('invoiced_at',null)
                    ->get();
            }
           
            if($invoice_type == 'marked_as_paid_invoices')
            {
                $$frequency_names  =   $all_orders->whereIn($company_id_name,$company_ids)
                    ->where('invoice_freq',$invoice_frequency)
                    ->where('invoiced_at', '!=', null)
                    ->where('confirmed_at',  null)
                    ->get();
                
            }
            if($invoice_type == 'confirmed_as_paid_invoices')
            {
                $$frequency_names  =   $all_orders->whereIn($company_id_name,$company_ids)
                    ->where('invoice_freq',$invoice_frequency)
                    ->where('confirmed_at', '!=', null)
                    ->get();
                
            }
            if($invoice_frequency   ==  1)
            {
                $$frequency_names  = $$frequency_names->groupBy(function($date) {
                    return \Carbon\Carbon::parse($date->created_at)->format('D-d-M-Y');
                });
            }
            elseif($invoice_frequency   ==  2)
            {
                $$frequency_names  = $$frequency_names->sortBy(function($date) {
                    return \Carbon\Carbon::parse($date->created_at)->format('U');
                })
                    ->groupBy(function($date) {
                        return \Carbon\Carbon::parse($date->created_at)->format('W-Y');
                    });
            }
            elseif($invoice_frequency   ==  3)
            {
                $$frequency_names  = $$frequency_names->groupBy(function($date) {
                    return \Carbon\Carbon::parse($date->created_at)->format('M-Y');
                });
            }
            foreach( $$frequency_names as $period    =>  $orders  )
            {
                $$frequency_names[$period]  =   $orders->groupBy($group_by_company_name);
            }
        }
        if(isset($invoice_frequency) && !isset($invoice_type))
        {
            /*  HERE YOU NEED DAILY,WEEKLY,MONTHLY INVOICES ACCORDING TO $invoice_frequency [1,2,3]*/
    
            if($invoice_frequency   ==  1)
            {
                $daily_invoices =  $all_orders->whereIn($company_id_name,$company_ids)
                    ->where('invoice_freq',1)
//                    ->get()
                    ->groupBy(function($date) {
                        return \Carbon\Carbon::parse($date->created_at)->format('D-d-M-Y');
                    });
        
                foreach($daily_invoices as $day    =>  $orders  )
                {
                    $daily_invoices[$day]  =   $orders->groupBy($group_by_company_name);
                }
            }
            elseif($invoice_frequency   ==  2)
            {
                $weekly_invoices =  $all_orders->whereIn($company_id_name,$company_ids)
                    ->where('invoice_freq',2)
//                    ->get()
                    ->sortBy(function($date) {
                        return \Carbon\Carbon::parse($date->created_at)->format('U');
                    })
                    ->groupBy(function($date) {
                        return \Carbon\Carbon::parse($date->created_at)->format('W-Y');
                    })
                ;
    
                foreach($weekly_invoices as $week    =>  $orders  )
                {
                    $weekly_invoices[$week]  =   $orders->groupBy($group_by_company_name);
                }
    
    
            }
            elseif($invoice_frequency   ==  3)
            {
                $monthly_invoices =  $all_orders->whereIn($company_id_name,$company_ids)
                    ->where('invoice_freq',3)
//                    ->get()
                    ->groupBy(function($date) {
                        return \Carbon\Carbon::parse($date->created_at)->format('M-Y');
                    });
    
                foreach($monthly_invoices as $month    =>  $orders  )
                {
                    $monthly_invoices[$month]  =   $orders->groupBy($group_by_company_name);
        
                }
            }
           /* $orders  =   $all_orders->whereIn($company_id_name,$company_ids)
                ->where('invoice_freq',$invoice_frequency)
                ->get()
                ->groupBy('invoice_freq');*/
        }
        
        ////ALL INVOICES
        if(!isset($invoice_frequency) && !isset($invoice_type))
        {
           
            $orders  =   $all_orders->whereIn($company_id_name,$company_ids)
//                ->get()
                ->groupBy('invoice_freq');
         
            foreach($orders as $order_freq   =>  $period_invoices)
            {
                if($order_freq   ==  1)
                {
                    $daily_invoices =  $all_orders->whereIn($company_id_name,$company_ids)
                        ->where('invoice_freq',1)
//                        ->get()
                        ->groupBy(function($date) {
                            return \Carbon\Carbon::parse($date->created_at)->format('D-d-M-Y');
                        });
            
                    foreach($daily_invoices as $day    =>  $orders  )
                    {
                        $daily_invoices[$day]  =   $orders->groupBy($group_by_company_name);
                    }
                }
                elseif($order_freq   ==  2)
                {
                    $weekly_invoices =  $all_orders->whereIn($company_id_name,$company_ids)
                        ->where('invoice_freq',2)
//                        ->get()
                        ->sortBy(function($date) {
                            return \Carbon\Carbon::parse($date->created_at)->format('U');
                        })
                        ->groupBy(function($date) {
                            return \Carbon\Carbon::parse($date->created_at)->format('W-Y');
                        })
                    ;
            
                    foreach($weekly_invoices as $week    =>  $orders  )
                    {
                        $weekly_invoices[$week]  =   $orders->groupBy($group_by_company_name);
                    }
            
            
                }
                elseif($order_freq   ==  3)
                {
                    $monthly_invoices =  $all_orders->whereIn($company_id_name,$company_ids)
                        ->where('invoice_freq',3)
//                        ->get()
                        ->groupBy(function($date) {
                            return \Carbon\Carbon::parse($date->created_at)->format('M-Y');
                        });
            
                    foreach($monthly_invoices as $month    =>  $orders  )
                    {
                        $monthly_invoices[$month]  =   $orders->groupBy($group_by_company_name);
                
                    }
                }
            }
        }
    
        
    
        $paid_invoices = DB::table('invoices')
            ->join('buyer_companies', 'invoices.buyer_company_id', '=', 'buyer_companies.id')
            ->join('seller_companies', 'invoices.seller_company_id', '=', 'seller_companies.id')
            ->whereIn('invoices.'.$company_id_name,$company_ids)
            ->select('invoices.*', 'buyer_company_name', 'seller_company_name')
            ->get()
            ->groupBy(['period',$opposite_owner.'_company_name'])->toArray();
        
       //dd($paid_invoices);
    
    
        $currencies = [];
     
    
        
        if($daily_invoices)    
        {
            
            $daily_invoices =  $daily_invoices->reverse()  ;
//            $daily_invoices_total      =   array_flip (array_keys($daily_invoices->toArray()));
            
            foreach($daily_invoices as $day  =>    $companies)
            {
                foreach($companies  as  $company    =>  $orders)
                {
                    $company_daily_invoices_total[$day][$company]   =  [] ;
                    $company_daily_order_ids[$day][$company]       =  [] ;
                    //$buyer_invoices[$day][$company]                    =  [] ;
                    foreach($orders as $order)
                    {
                        $order_currency             =   $order->currency;
                        //$orders_total           +=  $order->total_order_cost    ;
                        $company_orders_total   +=  $order->total_order_cost    ;
                        $company_daily_order_ids[$day][$company][] =   $order->id;
                        //$buyer_invoices[$order->buyer_company_id][$day][]                      =  $order->department;
                        $company_o_t_by_currency[$order_currency] = $company_orders_total;
                    }
    
                   
                   // $company_orders_total                                                   +=  $order->total_order_cost    ;
    
                    $company_daily_invoices_total[$day][$company] =   $company_o_t_by_currency;
                    
                    if($owner == 'seller')
                        $daily_total[$day] = $company_o_t_by_currency ;
                    elseif( $owner==    'buyer')
                        $daily_total[$day][$order_currency][] = $company_o_t_by_currency[$order_currency] ;
                    $company_orders_total    =   0;
                    $company_o_t_by_currency=[];
                    
                }
//                $daily_invoices_total[$day] =   $orders_total;
//                $orders_total    =   0;
            }
        }
      
        if($weekly_invoices)   
        {
            $weekly_invoices =  $weekly_invoices->reverse()  ;
           // $weekly_invoices_total              =   array_flip (array_keys($weekly_invoices->toArray()));
           
            foreach($weekly_invoices as $week  =>    $companies)
            {
                foreach($companies  as  $company    =>  $orders)
                {
                    $company_weekly_invoices_total[$week][$company]     =  [] ;
                    $company_weekly_order_ids[$week][$company]          =  [] ;
                  
                    
                    foreach($orders as $order)
                    {
                        $order_currency             =   $order->currency;
                      /*  $orders_total                                                           +=  $order->total_order_cost    ;*/
                        $company_orders_total                                                   +=  $order->total_order_cost    ;
                        $company_weekly_order_ids[$week][$company][]                            =    $order->id;
                       // $buyer_invoices[$order->buyer_company_id][$week][]                      =  $order->department;
                        //$buyer_invoices[$order->buyer_company_id][$week][$order->department]    =  0;
                        $company_o_t_by_currency[$order_currency] = $company_orders_total;
                       // $new_weekly_invoices_to_pay[$order->buyer_company_id][$week][$order->department][]  =   $order;
                    }
                    
                  //  $new_company_weekly_invoices_total[$order->buyer_company_id][$week][$order->department][$order->seller_company_id][]  =     $company_orders_total;
                    $company_weekly_invoices_total[$week][$company] =   $company_o_t_by_currency;
                    if($owner == 'seller')
                        $weekly_total[$week] = $company_o_t_by_currency ;
                    elseif( $owner==    'buyer')
                    $weekly_total[$week][$order_currency][] = $company_o_t_by_currency[$order_currency] ;
                    $company_orders_total    =   0;
                    $company_o_t_by_currency=[];
                }
                
//                $weekly_invoices_total[$week] =   $orders_total;
//                $orders_total    =   0;
            }  
        }
   
        if($monthly_invoices)  
        {
            
            /*$monthly_invoices_total    =   array_flip (array_keys($monthly_invoices->toArray()));*/
            $monthly_invoices =  $monthly_invoices->reverse()  ;
            foreach($monthly_invoices as $month  =>    $companies)
            {
                foreach($companies  as  $company    =>  $orders)
                {
                    $company_monthly_invoices_total[$month][$company]   =  [] ;
                    $company_monthly_order_ids[$month][$company]         =  [] ;
                    //$buyer_invoices[$month][$company]                    =  [] ;
    
                    foreach($orders as $order)
                    {
                        $order_currency             =   $order->currency;
                       /* $orders_total               +=  $order->total_order_cost    ;*/
                        $company_orders_total       +=  $order->total_order_cost    ;
                        
                        $company_monthly_order_ids[$month][$company] []  =   $order->id;
                       
                       // $buyer_invoices[$order->buyer_company_id][$month][$order->department]    =  0;
                        
                        $company_o_t_by_currency[$order_currency] = $company_orders_total;
                        
                     /*   $orders_total_by_currency[$order_currency] = $orders_total;*/
                       
                     
                    }
                    
   
                    $company_monthly_invoices_total[$month][$company] =   $company_o_t_by_currency;
                   
                   
                    if($owner == 'seller')
                        $monthly_total[$month] = $company_o_t_by_currency ;
                    elseif( $owner==    'buyer')
                        $monthly_total[$month][$order_currency][] = $company_o_t_by_currency[$order_currency] ;
                    $company_orders_total    =   0;
                    $company_o_t_by_currency=[];
                    
                }
              /*  $monthly_invoices_total[$month]  =   $orders_total_by_currency;*/
              
               /* $orders_total    =   0;*/
               /* $orders_total_by_currency = [];*/
            }
            
        }
        
        
 //dd($daily_total,$weekly_total,$monthly_total);
        //$monthly_invoices =  $monthly_invoices->reverse()  ;
   
       $company_totals    = [
           'daily_invoices'            =>  $company_daily_invoices_total,
           'weekly_invoices'           =>  $company_weekly_invoices_total,
           'monthly_invoices'          =>  $company_monthly_invoices_total
       ] ;
       $invoices    =   [
           'daily_invoices'            =>  $daily_invoices,
           'weekly_invoices'           =>  $weekly_invoices,
           'monthly_invoices'          =>  $monthly_invoices
       ] ;
        $totals    =   [
            'daily_invoices'      =>  $daily_total,
            'weekly_invoices'     =>  $weekly_total,
            'monthly_invoices'    =>  $monthly_total
        ] ;
        $order_ids    =   [
            'daily_invoices'      =>  $company_daily_order_ids,
            'weekly_invoices'     =>  $company_weekly_order_ids,
            'monthly_invoices'    =>  $company_monthly_order_ids
        ] ;
    
       // dd($company_totals);
        return [
            'invoices'          =>  $invoices,
            'totals'            =>  $totals,
            'company_totals'    =>  $company_totals,
            'order_ids'         =>  $order_ids,
            'company_ids'       =>  $company_ids,
            'paid_invoices'     =>  $paid_invoices
        ] ;
    }
    
    public function send_invoice($request)
    {
      $invoice_cost   =   DB::table('orders')
            ->whereIn('id',json_decode($request->order_ids,true))
            ->sum('total_order_cost');
        
       
        if(
        $invoice_id = DB::table('invoices')
            ->insertGetId([
                'buyer_company_id'  =>  $request->buyer_company_id,
                'seller_company_id' =>  $request->seller_company_id ,
                'period'            =>  $request->period,
                'invoice_freq'      =>  $request->invoice_freq,
                'order_ids'         =>  $request->order_ids, ///// ALREADY ENCODED FROM BLADE VIEW
                'invoice_cost'      =>  $invoice_cost,
                'sender_user_id'    =>  \Auth::guard('seller')->user()->id,
                'created_at'        =>  date('Y-m-d H:i:s'),
                'updated_at'        =>  date('Y-m-d H:i:s') ,
            ])
        )
        {
            $details  =   DB::table('invoices')
                ->join('buyer_companies', 'invoices.buyer_company_id', '=', 'buyer_companies.id')
                ->join('seller_companies', 'invoices.seller_company_id', '=', 'seller_companies.id')
                ->where('invoices.id',$invoice_id)
                ->get(['seller_companies.seller_company_name',
                    'seller_companies.seller_accountant_name',
                    'seller_companies.seller_accountant_phone_number',
                    'seller_companies.seller_accountant_email',
                    'buyer_companies.buyer_company_name',
                    'buyer_companies.buyer_accountant_name',
                    'buyer_companies.buyer_accountant_email',
                    'invoices.period',
                    'invoices.id',
                    'invoices.invoice_cost'
                     ])
                ->first();
         
            $invoice =    [
                'n_link'                        =>    '/invoice/'.$invoice_id.'/'. $request->buyer_company_id,
                'action'                        =>   'invoice_sent',
                'id'                    =>   $invoice_id,
                'buyer_company_id'      =>  $request->buyer_company_id,
                'seller_company_id'     =>  $request->seller_company_id ,
                'subject'               =>    __('You have a new invoice id invoice_id to be paid to seller_company',
                                            [
                                                    __('invoice_id') => $invoice_id,
                                                    'seller_company' => $details->seller_company_name
                                            ]),
               /* 'subject'               =>    __('You have a new invoice id invoice_id to be paid to seller_company',
                                                    [
                                                        __('invoice_id')=>$invoice_id,
                                                        'seller_company'=>$details->seller_company_name
                                                    ]),*/
              
            ];
           
            $view = 'invoice_to_be_paid';
            $details->action   =   'invoice_to_be_paid';
            $details->subject   =  __('You have a new invoice id invoice_id to be paid to seller_company',
                [
                    __('invoice_id') => $invoice_id,
                    'seller_company' => $details->seller_company_name
                ]);
           // dispatch( new InvoiceEmailJob($details,$view));
            BuyerNotificationEvent::dispatch($invoice);
            return true;
        }
        
        else
        {
            return false;
        }
        
    }
    
    public function mark_as_paid_invoice($request)
    {
       $order_ids   =   $this->get_order_ids($request);
     
       if(
        DB::table('invoices')
            ->where('buyer_company_id',$request->buyer_company_id)
            ->where('seller_company_id',$request->seller_company_id)
            ->where('period',$request->period)
            ->where('id',$request->invoice_id)
            ->update([
                'paid_at'           =>      date('Y-m-d H:i:s'),
                'payer_user_id'     =>      \Auth::guard('buyer')->user()->id
            ])
           
           &&
        
        DB::table('orders')
            ->whereIn('id',$order_ids)
            ->update(['invoiced_at'           =>      date('Y-m-d H:i:s') ])
        
            
        )
       {
           $details  =   DB::table('invoices')
               ->join('buyer_companies', 'invoices.buyer_company_id', '=', 'buyer_companies.id')
               ->join('seller_companies', 'invoices.seller_company_id', '=', 'seller_companies.id')
               ->where('invoices.id',$request->invoice_id)
               ->get(['seller_companies.seller_company_name',
                   'seller_companies.seller_accountant_name',
                   'seller_companies.seller_accountant_email',
                   'buyer_companies.buyer_company_name',
                   'buyer_companies.buyer_accountant_name',
                   'buyer_companies.buyer_accountant_email',
                   'buyer_companies.buyer_accountant_phone_number',
                   'invoices.period',
                   'invoices.id',
                   'invoices.paid_at',
                   'invoices.invoice_cost'
               ])
               ->first();
    
           $invoice =    [
               'n_link'                =>    '/invoice/'.$request->invoice_id.'/'. $request->seller_company_id,
               'action'                =>   'invoice_paid',
               'id'                    =>   $request->invoice_id,
               'buyer_company_id'      =>  $request->buyer_company_id,
               'seller_company_id'     =>  $request->seller_company_id ,
               'subject'               =>   __('Invoice id invoice_id was marked as paid by buyer_company',
                                                  [
                                                      __('invoice_id') =>   $request->invoice_id,
                                                     'buyer_company'         =>  $details->buyer_company_name
                                                  ]),
               
           ];
           $details->action   =   'invoice_paid';
           $details->subject   =   __('Invoice id invoice_id was marked as paid by buyer_company',
               [
                   __('invoice_id') =>   $request->invoice_id,
                   'buyer_company'         =>  $details->buyer_company_name
               ]);
           $view = 'invoice_paid';
           //dispatch( new InvoiceEmailJob($details,$view));
           SellerNotificationEvent::dispatch($invoice);
           return true;
       }
        
        else
            return false;
    }
    
    public function confirm_invoice($request)
    {
        $order_ids   =   $this->get_order_ids($request);
        
        if(
        DB::table('invoices')
            ->where('id',$request->invoice_id)
            ->update([
                'confirmed_at'             =>      date('Y-m-d H:i:s'),
                'confirmed_by_user_id'     =>      \Auth::guard('seller')->user()->id
            ])

        &&

        DB::table('orders')
            ->whereIn('id',$order_ids)
            ->update(['confirmed_at'           =>      date('Y-m-d H:i:s') ])
        )
        {
            $details  =   DB::table('invoices')
                ->join('buyer_companies', 'invoices.buyer_company_id', '=', 'buyer_companies.id')
                ->join('seller_companies', 'invoices.seller_company_id', '=', 'seller_companies.id')
                ->where('invoices.id',$request->invoice_id)
                ->get(['seller_companies.id as seller_company_id',
                    'seller_companies.seller_company_name',
                    'seller_companies.seller_accountant_name',
                    'seller_companies.seller_accountant_email',
                    'seller_companies.seller_accountant_phone_number',
                    'buyer_companies.id as buyer_company_id',
                    'buyer_companies.buyer_company_name',
                    'buyer_companies.buyer_accountant_name',
                    'buyer_companies.buyer_accountant_email',
                    'invoices.period',
                    'invoices.id',
                    'invoices.confirmed_at',
                    'invoices.invoice_cost'
                ])
                ->first();
    
            $invoice =    [
                'n_link'                =>    '/invoice/'.$request->invoice_id.'/'. $details->buyer_company_id,
                'action'                =>   'invoice_confirmed',
                'id'                    =>   $request->invoice_id,
                'buyer_company_id'      =>   $details->buyer_company_id,
                'seller_company_id'     =>   $details->seller_company_id ,
                'subject'               =>   __('Invoice id invoice_id was confirmed paid by seller_company',
                                                    [
                                                        __('invoice_id')      =>    $request->invoice_id,
                                                        'seller_company'            =>    $details->seller_company_name
                                                    ]),
                
            ];
            $view = 'invoice_payment_confirmed';
            $details->action   =   'invoice_payment_confirmed';
            $details->subject   =  __('Invoice id invoice_id was confirmed paid by seller_company',
                [
                    __('invoice_id')      =>    $request->invoice_id,
                    'seller_company'            =>    $details->seller_company_name
                ]);
            //dispatch( new InvoiceEmailJob($details,$view));
            BuyerNotificationEvent::dispatch($invoice);
            return true;
        }
        
        else
            return false;
    }
    
    private function get_order_ids($request)
    {
        return   json_decode(DB::table('invoices')
            ->where('id',$request->invoice_id)
            ->pluck('order_ids')->first(),true);
    }
}
