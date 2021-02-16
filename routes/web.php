<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('study', 'WelcomeController@study');
Route::get('/', 'WelcomeController@index');
Route::get('/new_country', 'WelcomeController@new_country');
Route::post('/new_country','WelcomeController@add_new_country');
Route::get('country_request/{email}/{token}','WelcomeController@country_request');

Auth::routes(['verify' => true]);

//// REGISTERING DIFFERENT USER ROLES
Route::get('/register_admin_admin', 'Auth\RegisterController@showRegistrationForm');
Route::post('/register_admin_admin', 'Auth\RegisterController@register');


Route::get('/register_seller_owner', 'Auth\RegisterController@showRegistrationForm');
Route::post('/register_seller_owner', 'Auth\RegisterController@register');

Route::get('register_seller_seller', 'Auth\RegisterController@showRegistrationForm');
Route::post('register_seller_seller', 'Auth\RegisterController@register');

Route::get('register_seller_accountant', 'Auth\RegisterController@showRegistrationForm');
Route::post('register_seller_accountant', 'Auth\RegisterController@register');

Route::get('register_buyer_owner', 'Auth\RegisterController@showRegistrationForm');
Route::post('register_buyer_owner', 'Auth\RegisterController@register');

Route::get('register_buyer_buyer', 'Auth\RegisterController@showRegistrationForm');
Route::post('register_buyer_buyer', 'Auth\RegisterController@register');

Route::get('register_buyer_accountant', 'Auth\RegisterController@showRegistrationForm');
Route::post('register_buyer_accountant', 'Auth\RegisterController@register');

////// DELEGATION OF RESPONSIBILITIES SELLER
/*Route::get('seller/delegate/{email}','Seller\DelegateController@delegate_selling');
Route::get('seller_accountant/delegate/{email}','Seller\DelegateController@delegate_accounting');*/

////// REGISTERING STAFF FROM EMAIL
Route::get('/delegations/{email}/{token}','DelegateController@delegations');
Route::get('/admin_delegations/{email}/{token}','DelegateController@admin_delegations');



///// ORDERS

Route::post('/order_placed','OrdersController@order_placed');

Route::get('/order/{order_id}/{company_id}', 'OrdersController@display_order')->middleware('buyer_seller_can:see-orders','is_owner');
Route::get('/orders/', 'OrdersController@orders')->middleware('buyer_seller_can:see-orders');
Route::get('/orders/{company_id}', 'OrdersController@orders')->middleware('buyer_seller_can:see-orders','is_owner');
Route::get('/orders/{department}/{company_id}', 'OrdersController@orders')->middleware('buyer_seller_can:see-orders','is_owner');

Route::post('order_dispatched', 'OrdersController@order_dispatched')->middleware('buyer_seller_can:interact-with-orders');
Route::post('order_delivered', 'OrdersController@order_delivered')->middleware('buyer_seller_can:interact-with-orders');
Route::post('order_delivery_confirmed', 'OrdersController@order_delivery_confirmed')->middleware('buyer_seller_can:interact-with-orders');
////// DUTIES


/*////// SELLER OWNER

Route::get('/seller_owner', 'Seller\OwnerController@index');

////// SELLER SELLER
Route::get('/seller_seller', 'Seller\SellerController@index');

////// SELLER ACCOUNTANT
Route::get('/seller_accountant', 'Seller\AccountantController@index');*/

////// SELLER COMPANY
Route::get('seller/company/{id}', 'Seller\CompanyController@index')->middleware('is_owner');
Route::get('edit_seller_company/{id}','Seller\CompanyController@edit')->middleware('is_owner');
Route::match(['put', 'patch'],'register_seller_company/{id}' , 'Seller\CompanyController@update')->middleware('is_owner');
Route::get('/buyers/{company_id}', 'Seller\CompanyController@buyers')->middleware('is_owner');
Route::get('/buyers_for_accountant/{company_id}', 'Seller\CompanyController@buyers_for_accountant')->middleware('seller_can:edit-payment-frequency','is_owner');

Route::post('register_seller_company' , 'Seller\CompanyController@store');
Route::get('create_seller_company', 'Seller\CompanyController@create');

///// DELIVERY LOCATIONS
Route::get('/delivery/location/{delivery_location_id}/{department}','Seller\DeliveryController@show')->middleware('seller_can:see-delivery-locations','is_owner');
Route::get('/delivery_locations', 'Seller\DeliveryController@locations')->middleware('seller_can:see-delivery-locations','is_owner');
Route::post('expand_delivery_locations', 'Seller\DeliveryController@expand_delivery_locations')->middleware('seller_can:add-delivery-locations');
Route::post('delete_delivery_location', 'Seller\DeliveryController@delete_delivery_location')->middleware('seller_can:delete-delivery-location');
Route::post('delete_delivery_department', 'Seller\DeliveryController@delete_delivery_department')->middleware('seller_can:delete-delivery-location');
Route::post('update_location_delivery_days', 'Seller\DeliveryController@update_location_delivery_days')->middleware('seller_can:edit-delivery-days');
Route::post('update_buyer_delivery_days', 'Seller\DeliveryController@update_buyer_delivery_days')->middleware('seller_can:edit-delivery-days');


//////  EXTENDED PRICE LISTS
Route::get('prices',
            'Seller\ExtendedPriceListController@create_extended_price_list')
            ->middleware('seller_can:see-default-prices');
Route::post('/prices', 'Seller\ExtendedPriceListController@create_extended_price_list')
    ->middleware('seller_can:see-default-prices');

Route::post('save_extended_price_list' , 'Seller\ExtendedPriceListController@store')->middleware('seller_can:edit-default-prices');
Route::post('update_extended_price_list' , 'Seller\ExtendedPriceListController@update')->middleware('seller_can:edit-default-prices');
Route::post('update_translations' , 'Seller\ExtendedPriceListController@update_translations')->middleware('seller_can:edit-default-prices');

Route::post('delete_product','Seller\ExtendedPriceListController@delete_product')->middleware('seller_can:delete-default-prices');
Route::post('delete_department/seller','Seller\ExtendedPriceListController@delete_department')->middleware('seller_can:delete-default-prices');

/*//////  BUYER OWNER
Route::get('/buyer_owner', 'Buyer\OwnerController@index');

////// BUYER BUYER
Route::get('/buyer_buyer', 'Buyer\BuyerController@index');

////// BUYER ACCOUNTANT
Route::get('/buyer_accountant', 'Buyer\AccountantController@index');*/

////// BUYER COMPANY
Route::get('/buyer/company/{company_id}', 'Buyer\CompanyController@index')->middleware('is_owner');
Route::get('create_buyer_company/{first?}', 'Buyer\CompanyController@create');
Route::get('edit_buyer_company/{id}','Buyer\CompanyController@edit')->middleware('is_owner');
Route::post('register_buyer_company' , 'Buyer\CompanyController@store');
Route::match(['put', 'patch'],'edit_buyer_company/{id}' , 'Buyer\CompanyController@update')->middleware('is_owner');

////// BUYER PRODUCT LIST
Route::middleware(['buyer_can:manage-products'])->group(function () {
    
    Route::get('product_list', 'Buyer\ProductListController@index');
    Route::post('product_list/check', 'Buyer\ProductListController@check');
    Route::post('product_list/save/{department}', 'Buyer\ProductListController@store');
    Route::post('product_list/delete', 'Buyer\ProductListController@delete');
    Route::get('product_list/show', 'Buyer\ProductListController@show');
});

///// BUYER ORDERS STATISTICS
Route::get('statistics/{company_id?}', 'StatisticsController@show');
Route::get('charts','StatisticsController@default_chart');
Route::post('chart','StatisticsController@chart');
Route::post('figures','StatisticsController@figures');


/////// ORDERING
Route::get('/ordering', 'Buyer\OrderingController@ordering');
Route::post('/ordering/sellers', 'Buyer\OrderingController@sellers_by_delivery_day');
Route::post('/ordering/place_order', 'Buyer\OrderingController@place_order');
Route::get('/ordering/form', 'Buyer\OrderingController@load_form');
Route::get('/ordering/alternative', 'Buyer\OrderingController@load_alternative');


///// BUYER DEPARTMENT
Route::get('/department/{department}', 'Buyer\DepartmentController@show')->middleware('is_owner');
//Route::get('/products/{department}/{company_id}', 'Buyer\DepartmentController@products')->middleware('is_owner');
Route::get('/sellers/{company_id}', 'Buyer\DepartmentController@sellers')->middleware('buyer_can:coordinate-requests','is_owner');
Route::get('/search_sellers/{company_id}', 'Buyer\DepartmentController@search_sellers')->middleware('buyer_can:coordinate-requests','is_owner');
Route::post('/toggle_buyer_seller','Buyer\DepartmentController@toggle_buyer_seller')->middleware('buyer_seller_can:de-activate-seller');



////// SELLER PRICING PRODUCT LIST
Route::get('/pricing/{buyer_company_id}/{department}/{seller_company_id}','Seller\PriceListController@create')
    ->middleware(/*'seller_can:price-product-list',*/'is_owner');
Route::post('/price_list/store', 'Seller\PriceListController@store')
    ->middleware('seller_can:edit-default-prices');
Route::get('/apply_default_prices', 'Seller\PriceListController@apply_default_prices')
    ->middleware('seller_can:price-product-list');

///// BUYER/SELLER PRODUCT LIST INTERACTIONS
Route::post('product_list_request', 'ProductListController@product_list_request')->middleware('buyer_seller_can:coordinate-requests');
Route::get('/requests/', 'ProductListController@cooperation_requests')->middleware('buyer_seller_can:coordinate-requests');
Route::get('/requests/{id}', 'ProductListController@cooperation_requests')->middleware('buyer_seller_can:coordinate-requests','is_owner');
Route::get('/requests/{department}/{id}', 'ProductListController@cooperation_requests')->middleware('buyer_seller_can:coordinate-requests','is_owner');

/////   REMOVING/ADDING PRODUCT FROM PRICE LISTS
Route::get('/price_list/{department}/{seller_company_id}/{buyer_company_id}', 'Buyer\PriceListController@show')->middleware('buyer_can:see-prices','is_owner');
Route::post('update_product', 'Buyer\PriceListController@update_product')->middleware('buyer_can:de-activate-product');

//// STAFF
Route::get('/staff/','StaffController@staff')->middleware('is_owner');
Route::get('/staff/{staff_id}/{company_id}','StaffController@show')->middleware('is_owner');
Route::post('duties','StaffController@duties');
Route::post('edit_staff','StaffController@edit_scope');
Route::post('create_staff','StaffController@create_staff');
Route::post('add_staff','StaffController@add_staff');
Route::post('edit_staff','StaffController@edit_scope');
Route::post('undelegate_staff','StaffController@undelegate_staff');
Route::post('/update_duties','StaffController@update_duties');


////// TRANSFERING COMPANIES BETWEEN STAFF
Route::post('/companies/transfer/form','TransferCompaniesController@form');
Route::post('/companies/transfer','TransferCompaniesController@transfer');


///// DISPLAYING COUNTIES AND COUNTIES_L4

Route::get('display_counties', 'LocationsController@display_counties');
Route::get('display_counties_4', 'LocationsController@display_counties_4');

///// INVOICES
Route::get('invoices/{period?}/{type?}','InvoiceController@index')->middleware('buyer_seller_can:see-invoices');
Route::post('mark_as_paid_invoice','InvoiceController@mark_as_paid_invoice')->middleware('buyer_can:mark-invoice-as-paid');
Route::post('send_invoice','InvoiceController@send_invoice')->middleware('seller_can:send-invoice');
Route::post('confirm_invoice','InvoiceController@confirm_invoice')->middleware('seller_can:confirm-invoice-as-paid');
Route::get('invoice/{invoice_id}/{company_id}','InvoiceController@show')->middleware('buyer_seller_can:see-invoices','is_owner');

///// PAYMENT FREQUENCY
Route::post('payment_frequency','PaymentFrequencyController@payment_frequency');

/////COUNTRY PREVIEW PAGES
Route::get('/country/{seifex_country_id}','CountryController@show');


//// PUSHER NOTIFICATIONS
Route::post('/pusher_notification', 'PusherNotificationController@pusher_notification');

//
///// ORDERS
//Route::post('/pusher_order_placed', 'PusherNotificationController@order_placed');
//Route::post('/pusher_order_dispatched', 'PusherNotificationController@order_dispatched');
//Route::post('/pusher_order_delivery_confirmed', 'PusherNotificationController@order_delivery_confirmed');
//
///// INVOICES
//Route::post('/pusher_invoice_sent', 'PusherNotificationController@invoice_sent');
//Route::post('/pusher_invoice_paid', 'PusherNotificationController@invoice_paid');
//Route::post('/pusher_invoice_confirmed', 'PusherNotificationController@invoice_confirmed');
//
//Route::post('/pusher_product_list', 'PusherNotificationController@product_list');
//Route::post('/pusher_company_activation', 'PusherNotificationController@company_activation');
//Route::post('/pusher_product_moved', 'PusherNotificationController@product_moved');
//Route::post('/pusher_payment_frequency', 'PusherNotificationController@payment_frequency');
//Route::post('/pusher_delivery_days', 'PusherNotificationController@delivery_days');

////TRANSLATIONS
Route::get('/load_english','TranslationMakerController@load_english');
//////INSERT SEIFEX LOCATIONS IDS
Route::get('/make_locations_table','LocationsController@make_locations_table');

////DATA about countries
Route::get('/info','Owner\CountryController@store2');

////DATA about countries
Route::get('/country_data','Owner\AllLevelsController@country_data');
Route::get('/get_world','Owner\AllLevelsController@get_world');
Route::get('/make_locations_table','Owner\AllLevelsController@make_locations_table');

//////LOADING MORE LANGUAGES
Route::post('/neighbour_languages','LanguageController@neighbour_languages');
Route::get('/load_neighbour_languages','LanguageController@load_neighbour_languages');
Route::get('/load_remaining_languages','LanguageController@load_remaining_languages');

//////LOADING MORE CURRENCIES
Route::post('/neighbour_currencies','CurrencyController@neighbour_currencies');
Route::get('/load_neighbour_currencies','CurrencyController@load_neighbour_currencies');
Route::get('/load_remaining_currencies','CurrencyController@load_remaining_currencies');

////// ADMIN ZONE


Route::get('/admin_admin', 'Admin\AdminController@index');

Route::middleware(['admin_can:create-departments'])->group(function () {
    
    
    Route::get('/departments','DefaultDepartmentController@index');
    Route::get('/department/{id}', 'DefaultDepartmentController@show');
    Route::get('create_department', 'DefaultDepartmentController@create');
    Route::post('create_department', 'DefaultDepartmentController@store');
    Route::get('edit_department/{id}', 'DefaultDepartmentController@edit');
    Route::match(['put', 'patch'],'department/{id}', 'DefaultDepartmentController@update');
    Route::delete('delete_department/{id}', 'DefaultDepartmentController@destroy');
    
});


Route::middleware(['admin_can:create-staff-duties'])->group(function () {
    
    Route::get('/staff_duties', 'DutyController@index');
    Route::get('/create_duty', 'DutyController@create');
    Route::post('/create_duty', 'DutyController@store');
    Route::get('/edit_duty/{duty_id}', 'DutyController@edit');
    Route::match(['put', 'patch'],'duty/{id}', 'DutyController@update');
    Route::delete('delete_duty/{id}', 'DutyController@destroy');
    
});
////// END OF ADMIN ZONE
