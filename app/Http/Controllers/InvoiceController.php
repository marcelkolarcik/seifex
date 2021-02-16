<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\InvoiceRepository;
use DB;
use App\Services\Role;

class InvoiceController extends Controller
{
    public $invoicer;
    public $role;
   public function __construct(InvoiceRepository $invoicer, Role $role)
   {
       $this->invoicer   =   $invoicer;
       $this->role   =   $role;
       $this->middleware('buyer_seller');
   }
   
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index($period   =   null,$type  =   null)
    {
        $invoice_period =   [
            1   =>  'daily',
            2   =>  'weekly',
            3   =>  'monthly'
        ];
    
        $invoice_type =   [
            1   =>  'unpaid_invoices',
            2   =>  'marked_as_paid_invoices',
            3   =>  'confirmed_as_paid_invoices'
        ];
    
        if($period && !isset($invoice_period[$period]))
        {
            abort('401');
        }
        
        $period_invoices    =   [];
        $owner = $this->role->get_guard();
        $owner_company_id   =   $owner.'_company_id';
        $company_table      =   $owner.'_companies';
        
        $unpaid_invoices             =   '';
        $marked_as_paid_invoices     =   '';
        $confirmed_as_paid_invoices  =   '';
    
        $daily_invoices              =   '';
        $weekly_invoices             =   '';
        $monthly_invoices            =   '';
    
        
    
        if(isset($invoice_type[$type]))
        {
            //////HERE IF IT ID STAFF => GET OWNER ID FROM DELEGATIONS......
            $invoices       =   $this->invoicer->create_invoices($period,$invoice_type[$type])['invoices'];
            $active_type    =   $invoice_type[$type];
            $$active_type   =   'bg-secondary text-light';
    
            $active_period    =   $invoice_period[$period];
            $$active_period   =   'bg-secondary text-light';
        }
        elseif(isset($period) && !isset($invoice_type[$type]))
        {
            $invoices  =   $this->invoicer->create_invoices($period)['invoices'];
            $active_period    =   $invoice_period[$period];
            $$active_period   =   'bg-secondary text-light';
        }
        elseif(!isset($period) && !isset($invoice_type[$type]))
        {
            $invoices  =   $this->invoicer->create_invoices()['invoices'];
        }
        
        $invoices_total = $this->invoicer->create_invoices();
        
        $totals             =   $invoices_total['totals'];
        $company_totals     =   $invoices_total['company_totals'];
        $order_ids          =   $invoices_total['order_ids'];
        $company_ids        =   $invoices_total['company_ids'];
        $paid_invoices      =   $invoices_total['paid_invoices'];
        
        $invoices_active    =   'active';
       
        return view('invoices.'.$owner,compact(
            'invoices',
            'totals',
            'company_totals',
            'owner_company_id',
            'order_ids',
            'company_ids',
            'paid_invoices',
            'invoices',
            'unpaid_invoices',
            'marked_as_paid_invoices',
            'confirmed_as_paid_invoices',
            'daily_invoices',
            'weekly_invoices',
            'monthly_invoices',
            'invoices_active'
        ));
    }
    ///// SELLER SENDING INVOICE TO BUYER
   public function  send_invoice(Request $request)
   {
       $invoice_sent        =   $this->invoicer->send_invoice($request);
       
       if($invoice_sent)
       {
          return ['status' => 'sent', 'title' => __('Invoice sent.')];
       }
       else
       {
           return ['status' => 'not sent', 'title' => __('Something went wrong.'),'text' => __('Please try again later.')];
          
       }
      
   }
    ///// BUYER MARKING INVOICE AS PAID
    public function  mark_as_paid_invoice(Request $request)
    {
        $invoice_marked_as_paid_invoice        =   $this->invoicer->mark_as_paid_invoice($request);
        if($invoice_marked_as_paid_invoice)
        {
            return ['status' => 'marked_as_paid', 'text' => __('Invoice marked as paid.')];
        }
        else
        {
            return ['status' => 'not_marked_as_paid', 'text' => __('Invoice not marked as paid.').' '.__('Please try again later.')];
        }
        
    }
    ///// SELLER IS CONFIRMING INVOICE AS PAID
    public function  confirm_invoice(Request $request)
    {
        $invoice_confirmed_as_paid_invoice        =   $this->invoicer->confirm_invoice($request);
        
        if($invoice_confirmed_as_paid_invoice)
        {
            return ['status' => 'confirmed_as_paid', 'text' => __('Invoice confirmed as paid.')];
        }
        else
        {
            return ['status' => 'not_confirmed_as_paid', 'text' => __('Invoice not confirmed as paid.').' '.__('Please try again later.')];
        }
        
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$owner_company_id)
    {
       
      if(! $invoice = DB::table('invoices')
            ->join('seller_companies', 'invoices.seller_company_id', '=', 'seller_companies.id')
            ->join('buyer_companies', 'invoices.buyer_company_id', '=', 'buyer_companies.id')
            ->where('invoices.id',$id)
            ->where('invoices.'.$this->role->company_id(),$owner_company_id)
            ->select(
                'invoices.*',
                'buyer_company_name',
                'buyer_companies.address as buyer_company_address' ,
                'buyer_email',
                'buyer_phone_number',
                'seller_company_name',
                'seller_companies.address as seller_company_address' ,
                'seller_name',
                'seller_email',
                'seller_phone_number',
                'seller_accountant_name',
                'seller_accountant_email',
                'seller_accountant_phone_number',
                'seller_companies.VAT_number as seller_VAT')
            ->first())
      {
          abort('404');
      }
       
     /* dd($invoice);*/
    
        $invoice_period =   [
            1   =>  'Daily',
            2   =>  'Weekly',
            3   =>  'Monthly'
        ];
       $orders =   DB::table('orders')->whereIn('id',json_decode($invoice->order_ids,true))->get()->toArray();
       $company_id    =   explode('_',\Auth::guard($this->role->get_guard())->user()->role)[0].'_company_id';
       $guard   =   $this->role->get_guard();
       return view('invoices.show', compact('invoice','orders','company_id','invoice_period','guard'));
    }

    

    
    
}
