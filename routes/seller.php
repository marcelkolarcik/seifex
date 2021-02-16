<?php

Route::group(['namespace' => 'Seller'], function() {
    
    Route::get('/', function () {
        
        $app = app();
        $controller = $app->make('\App\Http\Controllers\Seller\RoleController');
        
        if(Auth::guard('seller')->user()->role   ==  'seller_owner')
            return $controller->callAction('redirect', $parameters = array('owner'));
        if(Auth::guard('seller')->user()->role   ==  'seller_seller')
            return $controller->callAction('redirect', $parameters = array('staff'));
        if(Auth::guard('seller')->user()->role   ==  'seller_accountant')
            return $controller->callAction('redirect', $parameters = array('staff'));
        if(Auth::guard('seller')->user()->role   ==  'seller_delivery')
            return $controller->callAction('redirect', $parameters = array('staff'));
        
    })->middleware('seller.verified')->name('seller.dashboard');
    
   // Route::get('/', 'HomeController@index')->middleware('seller.verified')->name('seller.dashboard');

    // Login
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('seller.login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('seller.logout');

    // Register
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('seller.register');
    Route::post('register', 'Auth\RegisterController@register');

    // Passwords
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('seller.password.email');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('seller.password.request');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('seller.password.reset');

    // Verify
     Route::get('email/resend', 'Auth\VerificationController@resend')->name('seller.verification.resend');
     Route::get('email/verify', 'Auth\VerificationController@show')->name('seller.verification.notice');
     Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('seller.verification.verify');
     
     ///About
    Route::get('about/', 'AboutController@index');
    Route::get('about/delivery_locations', 'AboutController@delivery_locations');
    Route::get('about/prices', 'AboutController@prices');
    Route::get('about/our_buyers', 'AboutController@our_buyers');
    Route::get('about/{buyer_company_id}/buyer','AboutController@show');
    Route::get('about/{buyer_company_id}/product_lists','AboutController@product_lists');
    Route::get('about/{seller_company_id}/seller','AboutController@show_seller');

});
