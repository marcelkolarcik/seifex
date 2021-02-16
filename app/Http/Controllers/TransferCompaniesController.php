<?php

namespace App\Http\Controllers;

use App\Events\SellerNotificationEvent;
use App\Services\Company;
use App\Services\Role;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Arr;


class TransferCompaniesController extends Controller
{
    public $company;
    public $role;
    
    public function __construct(Company $company, Role $role)
    {
        $this->company = $company;
        $this->role = $role;
    }
    
    public function form(Request $request)
    {
       $staff   =   $this->company->for($this->role->get_owner_or_staff())[session()->get('company_id')]->staff[$request->staff_role];
       $to_sellers   =   [];
        $to_sellers[\Auth::guard('seller')->user()->name] = \Auth::guard('seller')->user()->id;
       foreach($staff as $employee)
       {
           if($employee['undelegated_at'] == null && $employee['accepted_at'] != null)
           {
               $to_sellers[$employee['staff_name']] = $employee['staff_id'];
           }
       }
     
        if($request->staff_role == 'seller_seller')
        {
            $details = DB::table('price_lists')
                ->join('sellers','sellers.id','=','price_lists.seller_id')
                ->join('buyer_companies','buyer_companies.id','=','price_lists.buyer_company_id')
                ->join('seller_companies','seller_companies.id','=','price_lists.seller_company_id')
                ->join('product_list_requests', function ($join) {
                    $join->on('product_list_requests.buyer_company_id', '=', 'buyer_companies.id');
                    $join->on('product_list_requests.seller_company_id', '=', 'seller_companies.id');
                  
                })
                ->where('price_lists.seller_company_id',session()->get('company_id'))
                ->get([
                    'sellers.name as seller_name',
                    'sellers.id as seller_id',
                    'buyer_companies.buyer_company_name',
                    'buyer_companies.id as buyer_company_id',
                    'buyer_companies.country',
                    'buyer_companies.county',
                    'buyer_companies.county_l4',
                    DB::raw('CONCAT(price_lists.id, "_", product_list_requests.id) as price_request_ids')
                ]);
          
          $sellers_with_companies = [];
          $price_request_ids = [];
         
          foreach($details as $detail)
          {
              $price_request_ids[$detail->buyer_company_id]['price_list_ids'][]    =   explode('_',$detail->price_request_ids)[0];
              $price_request_ids[$detail->buyer_company_id]['product_list_request_ids'][]    =   explode('_',$detail->price_request_ids)[1];
              $detail->price_request_ids    =   json_encode($price_request_ids[$detail->buyer_company_id]) ;
              $sellers_with_companies[$detail->seller_name][$detail->buyer_company_id] = $detail;
          }
          
        }
 
        return view('transfer_companies.form',compact('sellers_with_companies','to_sellers'));
    }
    public function transfer(Request $request)
    {
       
        $price_list_ids = [];
        $product_list_request_ids = [];
        $from_seller_ids    =   [];
     
        foreach($request->price_request_ids as $from_seller_id =>  $ids)
        {
            if(\Auth::guard('seller')->user()->id != $from_seller_id)
            $from_seller_ids[]  =   $from_seller_id;
            
            $ids =  json_decode($ids,true);
           
            $price_list_ids[] = $ids['price_list_ids'];
            $product_list_request_ids[] =  $ids['product_list_request_ids'];
        }
        $price_list_ids = array_unique(Arr::flatten($price_list_ids));
        $product_list_request_ids= array_unique(Arr::flatten($product_list_request_ids));
       
        DB::table('price_lists')
            ->whereIn('id',Arr::flatten($price_list_ids))
                ->update([
                    'seller_id'=> $request->to_seller,
                    'updated_at'    =>  date('Y-m-d H:i:s')]);
    
        DB::table('product_list_requests')
            ->whereIn('id',Arr::flatten($product_list_request_ids))
            ->update([
                'seller_id'     => $request->to_seller,
                'updated_at'    =>  date('Y-m-d H:i:s')]);
    
        $this->company->update('price_lists');
        
        /*NOTIFICATION TO STAFF THAT IS RECEIVING COMPANY, LOOSING COMPANY, NOT IF IT IS OWNER*/
    if($request->to_seller != \Auth::guard('seller')->user()->id)
    {
        $to_seller_details    =   [
            'n_link'                =>    '/buyers/'.session()->get('company_id'),
            'seller_id'            =>       $request->to_seller,
            'action'                =>    'company_transferred',
            'seller_name'           =>    \Auth::guard('seller')->user()->name,
            'subject'               =>   __('seller_name has transferred companies to you.',
                [
                    'seller_name'  =>   \Auth::guard('seller')->user()->name,
            
                ]),
        ];
        SellerNotificationEvent::dispatch($to_seller_details);
    }
       
    
        $from_seller_details    =   [
            'n_link'                =>    '/buyers/'.session()->get('company_id'),
            'seller_ids'            =>       $from_seller_ids,
            'action'                =>    'company_transferred_out',
            'seller_name'           =>    \Auth::guard('seller')->user()->name,
            'subject'               =>   __('seller_name has transferred companies from you.',
                [
                    'seller_name'  =>   \Auth::guard('seller')->user()->name,
            
                ]),
        ];
        SellerNotificationEvent::dispatch($from_seller_details);
        
        return back();
    }
}
