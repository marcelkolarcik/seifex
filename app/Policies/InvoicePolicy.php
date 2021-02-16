<?php

namespace App\Policies;

use App\Services\DutyCheckBuyerSeller;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Buyer;
use App\Seller;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public $check;
    
    public function __construct(DutyCheckBuyerSeller $check)
    {
        $this->check    =   $check;
    }
    public function mark_invoice_as_paid(Buyer $buyer) //// for accountant
    {
        return $this->check->buyer($buyer,'mark-invoice-as-paid');
    }
    
    public function buyer_see_invoices(Buyer $buyer)
    {
       return $this->check->buyer($buyer,'see-invoices');
    }
    public function seller_see_invoices(Seller $seller)
    {
        return $this->check->seller($seller,'see-invoices');
        
    }
    public function send_invoice(Seller $seller)
    {
        return $this->check->seller($seller,'send-invoice');
        
    }
    public function confirm_invoice_as_paid(Seller $seller)
    {
        return $this->check->seller($seller,'confirm-invoice-as-paid');
        
    }
    
    public function edit_payment_frequency(Seller $seller)
    {
        return $this->check->seller($seller,'edit-payment-frequency');
        
    }

    
   
}
