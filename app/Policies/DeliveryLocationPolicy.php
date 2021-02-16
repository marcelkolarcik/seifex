<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Seller;
use App\Services\DutyCheckBuyerSeller;

class DeliveryLocationPolicy
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
    
    public function see_delivery_locations(Seller $seller)
    {
        return $this->check->seller($seller,'see-delivery-locations');
    }
    
    public function add_delivery_locations(Seller $seller)
    {
        return $this->check->seller($seller,'add-delivery-locations');
    }
    
    public function edit_delivery_days(Seller $seller)
    {
        return $this->check->seller($seller,'edit-delivery-days');
    }
    
    public function delete_delivery_locations(Seller $seller)
    {
       
        return $this->check->seller($seller,'delete-delivery-locations');
    }
}
