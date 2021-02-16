<?php

namespace App\Policies;

use App\Services\DutyCheckBuyerSeller;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Buyer;
use App\Seller;

class OrderPolicy
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
    
    public function seller_see_orders(Seller $seller)
    {
        return $this->check->seller($seller,'see-orders');
    }
    
    public function buyer_see_orders(Buyer $buyer)
    {
        return $this->check->buyer($buyer,'see-orders');
    }
    
    public function seller_interact_with_orders(Seller $seller)
    {
        return $this->check->seller($seller,'interact-with-orders');
    }
    
    public function buyer_interact_with_orders(Buyer $buyer)
    {
        return $this->check->buyer($buyer,'interact-with-orders');
    }
    
}
