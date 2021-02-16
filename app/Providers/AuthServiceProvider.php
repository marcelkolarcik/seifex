<?php

namespace App\Providers;

use App\BuyerCompany;

use App\DefaultDepartment;
use App\DefaultPriceList;
use App\DeliveryLocation;
use App\Invoice;
use App\Order;
use App\Duty;
use App\Policies\DefaultPriceListPolicy;
use App\Policies\DeliveryLocationPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\OrderPolicy;
use App\Policies\DutyPolicy;
use App\Policies\DefaultDepartmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\ProductList;
use App\Policies\ProductListPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
       
       ProductList::class       =>  ProductListPolicy::class,
       Invoice::class           =>  InvoicePolicy::class,
       Order::class             =>  OrderPolicy::class,
       DeliveryLocation::class  =>  DeliveryLocationPolicy::class,
       DefaultPriceList::class  =>  DefaultPriceListPolicy::class,
       Duty::class              =>  DutyPolicy::class,
       DefaultDepartment::class =>  DefaultDepartmentPolicy::class,
       
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    
        ///////// BUYER PRODUCT LIST
        
        Gate::define('manage-products', 'App\Policies\ProductListPolicy@manage_products');
    
        Gate::define('de-activate-product', 'App\Policies\ProductListPolicy@de_activate_product');
        
        Gate::define('see-prices', 'App\Policies\ProductListPolicy@see_prices');
    
        Gate::define('price-product-list', 'App\Policies\ProductListPolicy@price_product_list');
    
        ///////// BUYER SELLER INTERACTION
    
        Gate::define('buyer-coordinate-requests', 'App\Policies\ProductListPolicy@buyer_coordinate_requests');
    
        Gate::define('seller-coordinate-requests', 'App\Policies\ProductListPolicy@seller_coordinate_requests');
    
        ///////// BUYER SELLER ACTIVATION
    
        Gate::define('de-activate-seller', 'App\Policies\ProductListPolicy@de_activate_seller');
    
        Gate::define('de-activate-buyer', 'App\Policies\ProductListPolicy@de_activate_buyer');
        
    
        ///////// INVOICES
    
        Gate::define('buyer-see-invoices', 'App\Policies\InvoicePolicy@buyer_see_invoices');
    
        Gate::define('seller-see-invoices', 'App\Policies\InvoicePolicy@seller_see_invoices');
    
        Gate::define('mark-invoice-as-paid', 'App\Policies\InvoicePolicy@mark_invoice_as_paid');
    
        Gate::define('send-invoice', 'App\Policies\InvoicePolicy@send_invoice');
        
        Gate::define('confirm-invoice-as-paid', 'App\Policies\InvoicePolicy@confirm_invoice_as_paid');
    
        Gate::define('edit-payment-frequency', 'App\Policies\InvoicePolicy@edit_payment_frequency');
        
    
        ///////// ORDERS
        
        Gate::define('seller-see-orders', 'App\Policies\OrderPolicy@seller_see_orders');
        
        Gate::define('buyer-see-orders', 'App\Policies\OrderPolicy@buyer_see_orders');
    
        Gate::define('seller-interact-with-orders', 'App\Policies\OrderPolicy@seller_interact_with_orders');
        
        Gate::define('buyer-interact-with-orders', 'App\Policies\OrderPolicy@buyer_interact_with_orders');
        
        /////////  DELIVERY LOCATIONS
    
        Gate::define('see-delivery-locations', 'App\Policies\DeliveryLocationPolicy@see_delivery_locations');
    
        Gate::define('add-delivery-locations', 'App\Policies\DeliveryLocationPolicy@add_delivery_locations');
    
        Gate::define('edit-delivery-days', 'App\Policies\DeliveryLocationPolicy@edit_delivery_days');
    
        Gate::define('delete-delivery-locations', 'App\Policies\DeliveryLocationPolicy@delete_delivery_locations');
    
        /////////  DEFAULT PRICE LIST
    
        Gate::define('see-default-prices', 'App\Policies\DefaultPriceListPolicy@see_default_prices');
    
        Gate::define('edit-default-prices', 'App\Policies\DefaultPriceListPolicy@edit_default_prices');
    
        Gate::define('delete-default-prices', 'App\Policies\DefaultPriceListPolicy@delete_default_prices');
    }
}
