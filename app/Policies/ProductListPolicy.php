<?php

namespace App\Policies;


use Illuminate\Auth\Access\HandlesAuthorization;
use App\Buyer;
use App\Seller;
use App\Services\DutyCheckBuyerSeller;

class ProductListPolicy
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
    
    public function manage_products(Buyer $buyer)
    {
        return $this->check->buyer($buyer,'manage-products');
    }
    
    public function buyer_coordinate_requests(Buyer $buyer)
    {
        return $this->check->buyer($buyer,'coordinate-requests');
    }
    
    public function seller_coordinate_requests(Seller $seller)
    {
        return $this->check->seller($seller,'coordinate-requests');
    }
    
    public function see_prices(Buyer $buyer)
    {
        return $this->check->buyer($buyer,'see-prices');
    }
    
    public function de_activate_seller(Buyer $buyer)
    {
        return $this->check->buyer($buyer,'de-activate-seller');
    }
    
    public function de_activate_buyer(Seller $seller)
    {
        return $this->check->seller($seller,'de-activate-buyer');
    }
    
    public function price_product_list(Seller $seller)
    {
        return $this->check->seller($seller,'price-product-list');
    }

    public function de_activate_product(Buyer $buyer)
    {
        return $this->check->buyer($buyer,'de-activate-product');
    }
    
    
   
  
}
