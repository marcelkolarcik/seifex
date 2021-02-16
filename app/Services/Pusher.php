<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 15-Nov-19
 * Time: 1:07
 */

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Gate;


class Pusher
{
    
    static function companies()
    {
        return Company::static_for(Role::owner_or_staff());
    }
    
    /*UPDATING STAFF SCOPE / DUTIES FOR STAFF*/
    static function staff_updated( Request $request ) /*STAFF IS LISTENING*/
    {
        if (\Auth::guard($request->details[ 'guard' ])->user()->id + 0 === $request->details[ 'staff_id' ] + 0) {
            Company::static_update([ 'staff', 'price_lists' ]);
            return 'ok';
        }
    }
    
    static function staff_accepted_job( Request $request ) /*OWNER IS LISTENING*/
    {
        if (\Auth::guard($request->details[ 'guard' ])->user()->email === $request->details[ 'owner_email' ]) {
            Company::static_update([ 'staff', $request->details[ 'guard' ] . '_companies' ]);
            return 'ok';
        }
    }
    
    static function staff_fired( Request $request )
    {
        if (\Auth::guard($request->details[ 'guard' ])->user()->email === $request->details[ 'staff_email' ]) {
            Company::static_update([ 'staff', $request->details[ 'guard' ] . '_companies' ]);
            return 'ok';
        }
    }
    /////// ORDERS
    ///
    ///
    ///  //// BUYER -> SELLER
    static function order_placed( Request $request )
    {
        
        if (in_array($request->details[ 'seller_company_id' ], session('seller_company_ids')) && Auth::guard('seller')->user()
            && Gate::forUser(\Auth::guard('seller')->user())->allows('seller-see-orders'))//// seller is listening
        {
            
            return 'ok';
        }
        
    }
    
    //// SELLER -> BUYER
    ///
    static function order_dispatched( Request $request )
    {
        
        if (in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids')) && Auth::guard('buyer')->user()
            && Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-see-orders')) //// buyer is listening
            
            return 'ok';
    }
    
    //// SELLER -> BUYER
    static function order_delivered( Request $request )
    {
        
        if (in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids')) && Auth::guard('buyer')->user()
            && Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-see-orders')) //// buyer is listening
            
            return 'ok';
    }
    
    ///  //// BUYER -> SELLER
    static function order_delivery_confirmed( Request $request )
    {
        if (in_array($request->details[ 'seller_company_id' ], session('seller_company_ids')) && Auth::guard('seller')->user()
            && Gate::forUser(\Auth::guard('seller')->user())->allows('seller-see-orders')) //// seller is listening
            
            return 'ok';
    }
    
    /////// INVOICES
    ///
    ///
    ///
    //// SELLER -> BUYER
    static function invoice_sent( Request $request )
    {
        if (in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids')) && Auth::guard('buyer')->user()
            && Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-see-invoices')) //// buyer is listening
            
            return 'ok';
    }
    
    //// BUYER -> SELLER
    static function invoice_paid( Request $request )
    {
        if (in_array($request->details[ 'seller_company_id' ], session('seller_company_ids')) && Auth::guard('seller')->user()
            && Gate::forUser(\Auth::guard('seller')->user())->allows('seller-see-invoices')) //// seller is listening
            
            return 'ok';
    }
    
    //// SELLER -> BUYER
    static function invoice_confirmed( Request $request )
    {
        if (in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids')) && Auth::guard('buyer')->user()
            && Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-see-invoices')) //// buyer is listening
            
            return 'ok';
    }
    
    
    ////////////// PRODUCT LIST  REQUESTS
    /// BUYER OR SELLER IS REQUESTING OR SENDING PRODUCT LIST
    static function cooperation_requests( Request $request )
    {
        //// SELLER -> BUYER
        if ($request->details[ 'activator' ] === 'seller' && Auth::guard('buyer')->user()
            && Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-coordinate-requests')
            && in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids'))) //// buyer is listening
        {
            Company::static_update([ 'product_lists', 'buyer_companies', 'seller_companies' ]);
            return 'ok';
            
        } //// BUYER -> SELLER
        if ($request->details[ 'activator' ] === 'buyer' && Auth::guard('seller')->user()
            && Gate::forUser(\Auth::guard('seller')->user())->allows('seller-coordinate-requests')
            && in_array($request->details[ 'seller_company_id' ], session('seller_company_ids'))) //// seller is listening
        {
            Company::static_update([ 'product_lists', 'buyer_companies', 'seller_companies', 'cooperation_requests' ]);
            return 'ok';
            
        }
    }
    
    //////BUYER OR SELLER IS ACTIVATING OPPOSITE COMPANY
    static function company_activation( Request $request )
    {
        //// SELLER -> BUYER
        if ($request->details[ 'activator' ] === 'seller' && Auth::guard('buyer')->user()
            && Gate::forUser(\Auth::guard('buyer')->user())->allows('de-activate-seller')
            && in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids'))) //// buyer is listening
        {
            Company::static_update([ 'companies', 'price_lists' ]);
            return 'ok';
        }
        //// BUYER -> SELLER
        if ($request->details[ 'activator' ] === 'buyer' && Auth::guard('seller')->user()
            && Gate::forUser(\Auth::guard('seller')->user())->allows('de-activate-buyer')
            && in_array($request->details[ 'seller_company_id' ], session('seller_company_ids'))) //// seller is listening
        {
            Company::static_update([ 'companies', 'price_lists' ]);
            return 'ok';
        }
    }
    
    //// BUYER DISABLING / ENABLING PRODUCTS FROM PRICE LIST
    //// BUYER -> SELLER
    static function product_moved( Request $request )
    {
        if (in_array($request->details[ 'seller_company_id' ], session('seller_company_ids')) && Auth::guard('seller')->user()
            && Gate::forUser(\Auth::guard('seller')->user())->allows('price-product-list'))//// seller is listening
        {
            Company::static_update([ 'price_lists', 'price_lists_extended' ]);
            return 'ok';
        }
        
    }
    
    ////// SELLER IS CHANGING PAYMENT FREQUENCY OF INVOICES, BUYER IS LISTENING
    ///   //// SELLER -> BUYER
    static function payment_frequency( Request $request )
    {
        if (in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids')) && Auth::guard('buyer')->user()) //// buyer is listening
        {
            Company::static_update('price_lists');
            
            if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                return 'ok';
        }
        
    }
    
    ////// SELLER IS CHANGING DELIVERY DAYS FOR BUYER, BUYER IS LISTENING
    ///
    ///   //// SELLER -> BUYER
    static function delivery_days( Request $request )
    {
        if (in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids')) && Auth::guard('buyer')->user()) //// buyer is listening
        
        {
            Company::static_update('price_lists');
            
            if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                return 'ok';
        }
    }
    
    
    
    ////// SELLER IS DELETING DELIVERY DEPARTMENT, => PRICE LISTS
    ///  FOR BUYERS WERE DELETED, BUYER NEEDS TO UPDATE HIS PRICE LISTS
    ///   //// SELLER -> BUYER
    static function delivery_department_deleted( Request $request )//// buyer is listening
    {
        $companies = Company::static_for(Role::owner_or_staff());
        
        /*SELLER COMPANIES THAT HAD PRICED BUYERS PRODUCTS*/
        $bc_ids = array_keys($companies);
        
        if ($bc_ids != []) {
            foreach ($bc_ids as $bc_id) {
                if (isset($companies[ $bc_id ]
                        ->price_lists
                    [ $request->details[ 'deleted_department' ] ]
                    [ $request->details[ 'seller_company_id' ] ])) {
                    session()->put('company_id', $bc_id);
                    Company::static_update('price_lists');
                    
                    if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                        return 'ok';
                    
                }
            }
            
        }
    }
    
    ////// SELLER IS DELETING DELIVERY LOCATION, => PRICE LISTS
    ///  FOR BUYERS WERE DELETED, BUYER NEEDS TO UPDATE HIS PRICE LISTS
    ///   //// SELLER -> BUYER
    static function delivery_location_deleted( Request $request )//// buyer is listening
    {
        $companies = Pusher::companies();
        
        /*SELLER COMPANIES THAT HAD PRICED BUYERS PRODUCTS*/
        $bc_ids = array_keys($companies);
        
        if ($bc_ids != []) {
            
            foreach ($bc_ids as $bc_id) {
                
                if (isset($companies[ $bc_id ]
                        ->price_lists
                    [ $request->details[ 'department' ] ]
                    [ $request->details[ 'seller_company_id' ] ])) {
                    session()->put('company_id', $bc_id);
                    Company::static_update([ 'price_lists', 'buyer_companies' ]);
                    
                    if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                        return 'ok';
                }
            }
            
        }
    }
    
    static function delivery_locations_for_sellers( Request $request )
    {
        if (in_array(\Auth::guard('seller')->user()->id, $request->details[ 'seller_ids' ])
            && Gate::forUser(\Auth::guard('seller')->user())->allows('see-delivery-locations')) {
            Company::static_update('delivery_locations');
            return 'ok';
        }
    }
    ////// BUYER IS UPDATING PRODUCT LIST ,SELLER IS LISTENING
    ///   //// BUYER -> SELLER
    static function product_list_updated( Request $request )//// SELLER is listening
    {
        $companies = Pusher::companies();
        
        /*BUYER COMPANIES THAT SELLER PRICED PRODUCTS*/
        $sc_ids = array_keys($companies);
        
        if ($sc_ids != []) {
            foreach ($sc_ids as $sc_id) {
                /*IF SELLER HAS BUYER'S PRODUCT LIST AND BUYER HAS UPDATED IT,
                WE WILL UPDATE PRODUCT LIST FOR SELLER AS WELL*/
                if (isset($companies[ $sc_id ]
                        ->product_lists
                    [ $request->details[ 'buyer_company_id' ] ]
                
                )) {
                    session()->put('company_id', $sc_id);
                    Company::static_update('product_lists');
                    return 'ok';
                }
            }
            
        }
    }
    
    static function product_deleted( Request $request )
    {
        $companies = Pusher::companies();
        
        $bc_ids = array_keys($companies);
        
        if ($request->details[ 'buyer_ids' ] != [ 0 ]) {
            
            $bc_ids = array_intersect($request->details[ 'buyer_ids' ], $bc_ids);
        } elseif ($request->details[ 'buyer_ids' ] == [ 0 ]) {
            
            $bc_ids = [];
        }
        if ($bc_ids != []) {
            foreach ($bc_ids as $bc_id) {
                session()->put('company_id', $bc_id);
                Company::static_update('price_lists');
                
            }
        }
        
    }
    
    ////// SELLER IS EDITING / CREATING PRICE LIST FOR BUYER
    /// ///   //// SELLER -> BUYER
    static function price_list_updated( Request $request )
    {
        if (in_array($request->details[ 'buyer_company_id' ], session('buyer_company_ids')) && Auth::guard('buyer')->user()
        ) //// buyer is listening
        
        {
            Company::static_update('price_lists');
            
            if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                return 'ok';
        }
    }
    
    static function price_list_updated_for_sellers( Request $request )
    {
        if (\Auth::guard('seller')->user()->id == $request->details[ 'seller_id' ]
            && Gate::forUser(\Auth::guard('seller')->user())->allows('seller-coordinate-requests')) {
            Company::static_update([ 'price_lists_extended', 'price_lists' ]);
            return 'ok';
        }
    }
    
    /*SELLER UPDATED HIS EXTENDED PRICE LIST*/
    static function price_list_extended_updated( Request $request )//// buyer is listening
    {
        $companies = Pusher::companies();
        
        /*SELLER COMPANIES THAT HAD PRICED BUYERS PRODUCTS*/
        $bc_ids = array_keys($companies);
        
        /*NORMAL UPDATING OF PRICES, WE WILL FIND OUT IF BUYER'S COMPANY PRICES WERE UPDATED*/
        if (isset($request->details[ 'buyer_ids' ]) && $request->details[ 'buyer_ids' ] != [ 0 ]) {
            
            $bc_ids = array_intersect($request->details[ 'buyer_ids' ], $bc_ids);
        } elseif (isset($request->details[ 'buyer_ids' ]) && $request->details[ 'buyer_ids' ] == [ 0 ]) {
            
            $bc_ids = [];
        }
        
        if ($bc_ids != []) {
            
            foreach ($bc_ids as $bc_id) {
                $price_list = isset($companies[ $bc_id ]->price_lists
                    [ $request->details[ 'department' ] ]
                    [ $request->details[ 'seller_company_id' ] ])
                    ? $companies[ $bc_id ]->price_lists
                    [ $request->details[ 'department' ] ]
                    [ $request->details[ 'seller_company_id' ] ]
                    : null;
                
                
                /*NORMAL UPDATING OF PRICES*/
                if (!isset($request->details[ 'currency' ]) && isset($price_list) ||
                    
                    /*CHANGING CONVERSION RATE*/
                    isset($request->details[ 'currency' ]) && isset($price_list->currency)
                    && $price_list->currency === $request->details[ 'currency' ]) {
                    session()->put('company_id', $bc_id);
                    session()->put('seller_updated_prices_id', $request->details[ 'seller_company_id' ]);
                    Company::static_update('price_lists');
                    
                    if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                        return 'ok';
                }
            }
            
        }
    }
    
    /*ONE OF THE SELLERS IS UPDATING EXTENDED PRICE LIST, WE NEED TO UPDATE LOGGED IN SELLERS EXTENDED PRICE LIST*/
    static function price_list_extended_updated_for_sellers( Request $request )
    {
        if (in_array(\Auth::guard('seller')->user()->id, $request->details[ 'seller_ids' ])
            && Gate::forUser(\Auth::guard('seller')->user())->allows('see-default-prices')) {
            Company::static_update('price_lists_extended');
            return 'ok';
        }
    }
    
    /*SELLER DELETED DELIVERY DEPARTMENT*/
    static function department_deleted( Request $request )//// buyer is listening
    {
        $companies = Pusher::companies();
        
        /*SELLER COMPANIES THAT HAD PRICED BUYERS PRODUCTS*/
        $bc_ids = array_keys($companies);
        
        if ($bc_ids != []) {
            
            foreach ($bc_ids as $bc_id) {
                
                if (isset($companies[ $bc_id ]
                        ->price_lists
                    [ $request->details[ 'department' ] ]
                    [ $request->details[ 'seller_company_id' ] ])) {
                    session()->put('company_id', $bc_id);
                    Company::static_update('price_lists');
                    
                    if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                        return 'ok';
                }
            }
            
        }
    }
    
    ////// SELLER IS UPDATING CONVERSION RATE,
    ///   //// SELLER -> BUYER
    static function conversion_rate_changed( Request $request )//// buyer is listening
    {
        $companies = Pusher::companies();
        
        /*SELLER COMPANIES THAT HAD PRICED BUYERS PRODUCTS*/
        $bc_ids = array_keys($companies);
        
        if ($bc_ids != []) {
            foreach ($bc_ids as $bc_id) {
                if (isset($companies[ $bc_id ]
                        ->price_lists
                    [ $request->details[ 'department' ] ]
                    [ $request->details[ 'seller_company_id' ] ])) {
                    session()->put('company_id', $bc_id);
                    Company::static_update('price_lists');
                    
                    if (Gate::forUser(\Auth::guard('buyer')->user())->allows('see-prices'))
                        return 'ok';
                }
            }
            
        }
    }
    
    /*SELLER OWNER IS TRANSFERRING COMPANIES TO NEW STAFF SELLER*/
    static function company_transferred( Request $request )
    {
        if (\Auth::guard('seller')->user()->id == $request->details[ 'seller_id' ]
            && Gate::forUser(\Auth::guard('seller')->user())->allows('seller-coordinate-requests')) {
            Company::static_update([ 'price_lists_extended', 'price_lists' ]);
            return 'ok';
        }
    }
    
    /*SELLER OWNER IS TRANSFERRING COMPANIES FROM  STAFF SELLER*/
    static function company_transferred_out( Request $request )
    {
        if (in_array(\Auth::guard('seller')->user()->id, $request->details[ 'seller_ids' ])
            && Gate::forUser(\Auth::guard('seller')->user())->allows('seller-coordinate-requests')) {
            Company::static_update([ 'price_lists_extended', 'price_lists' ]);
            return 'ok';
        }
    }
}

