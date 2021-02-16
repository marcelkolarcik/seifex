<?php

namespace App\Http\Controllers;

use App\Events\BuyerNotificationEvent;
use App\Jobs\PaymentFrequencyEmailJob;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Strings;
use App\Services\Role;
use App\Services\Company;
class PaymentFrequencyController extends Controller
{
    public $strings;
    public $company;
    public $role;
    
    public function __construct( Strings $strings,Role $role , Company $company)
    {
        $this->strings  =   $strings;
        $this->role     =   $role;
        $this->company  =   $company;
        $this->middleware('seller.auth:seller');
    }
    public function payment_frequency(Request $request)
    {
           DB::table('price_lists')
               ->where( 'buyer_company_id', $request->buyer_company_id)
               ->where( 'seller_company_id',  $request->seller_company_id)
               ->where( 'department',  $request->department)
               ->update([
                   'payment_frequency' => $request->payment_frequency,
                   'updated_at' => date('Y-m-d H:i:s'),
               ]);
    
        $this->company->update('price_lists');
        
        $seller_details = $this->company->for($this->role->get_owner_or_staff())[$request->seller_company_id];
        $buyer_details  =   $this->company->for($this->role->get_owner_or_staff())[$request->seller_company_id]->buyer_companies[$request->buyer_company_id];
      
           $details=[
               'n_link'                =>    '/department/'.$request->department.'/'.$request->buyer_company_id,
               'action'                =>   'payment_frequency',
               'buyer_email'        =>  $buyer_details['buyer_email'],
               'buyer_company_name' =>  $buyer_details['company_name'],
               'buyer_company_id'   =>  $request->buyer_company_id,
               'seller_company_id'  =>  $request->seller_company_id,
               'subject'            =>  __('Your invoice payment frequency for seller_company was changed to payment_frequency.',
                   [
                       'seller_company'     =>  $seller_details->seller_company_name,
                       'payment_frequency'  =>  $this->strings->payment_frequency()[$request->payment_frequency]
                   ]),
              
           ];
           
           /////    EMAIL
          // PaymentFrequencyEmailJob::dispatch($details);
           
           /////    PUSHER
           BuyerNotificationEvent::dispatch($details);
           
           return ['status'=>'updated', 'text'=>__('Payment frequency updated !')];
    }
}
