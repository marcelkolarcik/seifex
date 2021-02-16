<?php

namespace App\Http\Controllers\Buyer;


use App\Events\SellerNotificationEvent;
use App\Services\Currency;
use Illuminate\Http\Request;
use DB;
use App\Services\Role;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProductMovedEmailJob;
use App\Http\Controllers\Controller;
use App\Services\Company;

class PriceListController extends Controller
{
    public $role;
    public $company;
    
    public function __construct( Role $role, Company $company )
    {
        $this->role     =   $role;
        $this->company  =   $company;
        $this->middleware('buyer.auth:buyer');
    }
    
    private function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
    public function update_product(Request $request)
    {
      
        
        /////// DISABLING AND ENABLING PRODUCT
        if($request->ajax())
        {
            $product            =   $request->product;
            $seller_company_id  =   $request->seller_company_id;
            $buyer_company_id   =   $request->buyer_company_id;
            $department         =   $request->department;
            $action             =   $request->action;
       
            if($product && $seller_company_id)
            {
              
                ///// THIS IS FOR THE INDIVIDUAL BUYER PRICE LISTS FROM SELLERS
                ///
                $price_list = $this->companies()[$buyer_company_id]->price_lists[$department][$seller_company_id]->price_list;
             
              
                ///// seller doesnt have the product anymore, price == 0 or product deleted
                if(!isset($price_list[$product]) || $price_list[$product]['price_per_kg'] == 0 )
                {
                   
                    return ['status'=>'no product','text'=>__('Product is not available at the moment !',['Product'=>$request->product])];
                }
         
                if($action == 'remove') $price_list[$product]['unset']  = intval($price_list[$product]['unset']) + 1  ;
                if($action == 'add') $price_list[$product]['unset'] = intval($price_list[$product]['unset']) - 1  ;
               
                DB::table('price_lists')
                    ->where( 'department', $department)
                    ->where( 'buyer_company_id', $buyer_company_id)
                    ->where( 'seller_company_id', $seller_company_id)
                    ->update([ 'price_list' => json_encode($price_list),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s') ]);
    
              $this->company->update('price_lists');
                ///// THIS IS FOR SELLERS EXTENDED DEFAULT PRODUCT LIST
                $sellers_extended_price_lists =
                    $this->companies()[$buyer_company_id]->seller_price_lists_extended[$seller_company_id][$department];
                
               
                $buyer_details   =   $this->companies()[$buyer_company_id];
                $seller_details   =   $this->companies()[$buyer_company_id]->seller_companies[$seller_company_id];
              
        
                $company_names = [
                    'buyer_company_name'    =>  $buyer_details->buyer_company_name,
                    'seller_company_name'   =>  $seller_details['company_name']
                ];
               
               
            
                empty($sellers_extended_price_lists[$product]['unset']) ? $unset = 0 :  $unset = intval($sellers_extended_price_lists[$product]['unset'] );
              
                if($action == 'remove'){
                    $sellers_extended_price_lists[$product]['unset'] = $unset + 1  ;
                    $move = __('removed');
                    $to_from    =   __('from');
                    
                    $subject =  __('Product moved_product was moved to_from price list of buyer_company.',
                        [
                            'moved_product' =>  explode('+',$request->product)[0],
                            'moved'         =>  $move,
                            'buyer_company' =>  $buyer_details->buyer_company_name,
                            'to_from'       =>  $to_from
                        ]);
                   
                    $movement = 1;
                }
                if($action == 'add') {
                    $sellers_extended_price_lists[$product]['unset'] = $unset - 1  ;
                    $move = __('added');
                    $to_from    =   __('to');
                    
                    $subject = $subject =  __('Product moved_product was moved to_from price list of buyer_company.',
                        [
                            'moved_product' =>  explode('+',$request->product)[0],
                            'moved'         =>  $move,
                            'buyer_company' =>  $buyer_details->buyer_company_name,
                            'to_from'       =>  $to_from
                        ]);
                    $movement = 2;
                }
              
                DB::table('price_lists_extended')
                    ->where( 'department', $department)
                    ->where('seller_company_id',$seller_company_id)
                    ->where('deleted_at',null)
                    ->update([ 'price_list' => json_encode($sellers_extended_price_lists),
                    ]) ;
            }
            
            if( !$moves = DB::table('product_moves')
                ->where('buyer_company_id',$buyer_company_id)
                ->where('seller_company_id',$seller_company_id)
                ->where('product_name',$product)
                ->get('moves')
                ->first()
            )
            {
                $moves=[];
                ///// insert new here
                array_push($moves,[ $movement => [\Auth::guard('buyer')->user()->name,date('Y-m-d H:i:s') ]]);
                
                DB::table('product_moves')->insert([
                   'buyer_company_id'   =>  $buyer_company_id,
                   'seller_company_id'  =>  $seller_company_id,
                   'product_name'       =>  $product,
                   'latest_move'        =>  $movement,
                   'moves'              =>  json_encode($moves),
                   'updated_at'         =>  date('Y-m-d H:i:s'),
                   'created_at'         =>  date('Y-m-d H:i:s')
                ]);
            }
            else
            {
               // dd(json_decode($moves->moves,true),$move);
                ///// update here
                $moves = json_decode($moves->moves,true);
                array_push($moves,[ $movement => [\Auth::guard('buyer')->user()->name , date('Y-m-d H:i:s') ]]);
                
                DB::table('product_moves')
                    ->where('buyer_company_id',$buyer_company_id)
                    ->where('seller_company_id',$seller_company_id)
                    ->where('product_name',$product)
                    ->update([
                        'latest_move'   =>  $movement,
                        'moves'         => json_encode($moves),
                        'updated_at'    =>  date('Y-m-d H:i:s')
                    ]);
            }
            
          
            
            ////PUSHER NOTIFICATIONS
          
            $details= [
             'n_link'                =>    '/pricing/'.$request->buyer_company_id.'/'. $request->department.'/'. $request->seller_company_id,
             'action'                =>   'product_moved',
            'product'                =>   explode('+',$request->product)[0],
            'seller_company_id'      =>   $request->seller_company_id,
            'seller_email'           =>   $seller_details['seller_email'],
            'buyer_company_id'       =>   $request->buyer_company_id,
            'department'             =>   $request->department,
            'subject'                =>   $subject,
            'company_names'          =>   $company_names
            ];
            
            SellerNotificationEvent::dispatch($details);
    
            //// EMAIL
            dispatch(new ProductMovedEmailJob($details));
            
            return ['status'=>'updated','text'=>__('Product was updated !',['Product'=>explode('+',$request->product)[0]])];
        }
    }
    
    public function show($department,$seller_company_id,$buyer_company_id)
    {
        
        $price_list_data        =   $this->companies()[$buyer_company_id]->price_lists[$department][$seller_company_id];
        $currency               =   Currency::add_data_to_currency([$price_list_data->currency] ,'string') ;
        $price_list             =   $price_list_data->price_list;
        $seller_company_name    =   $price_list_data->seller_company_name;

        return view('buyer.department.price_list', compact('currency',
            'price_list',
            'department',
            'seller_company_id',
            'buyer_company_id',
            'seller_company_name'));
       
    }
}
