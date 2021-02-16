<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Seller;
use App\Services\DutyCheckBuyerSeller;

class DefaultPriceListPolicy
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
    
    public function see_default_prices(Seller $seller)
    {
        return $this->check->seller($seller,'see-default-prices');
    }
    
    public function edit_default_prices(Seller $seller)
    {
        return $this->check->seller($seller,'edit-default-prices');
    }
    
    public function delete_default_prices(Seller $seller)
    {
        return $this->check->seller($seller,'delete-default-prices');
    }
}
