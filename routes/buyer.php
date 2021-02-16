<?php

Route::group(['namespace' => 'Buyer'], function() {
    
    Route::get('/', function () {
        /* $users[] = Auth::user();
		 $users[] = Auth::guard()->user();
		 $users[] = Auth::guard('buyer')->user();*/
        
        $app = app();
        $controller = $app->make('\App\Http\Controllers\Buyer\RoleController');
        
        if(Auth::guard('buyer')->user()->role   ==  'buyer_owner')
            return $controller->callAction('redirect', $parameters = array('owner'));
        if(Auth::guard('buyer')->user()->role   ==  'buyer_buyer')
            return $controller->callAction('redirect', $parameters = array('staff'));
        if(Auth::guard('buyer')->user()->role   ==  'buyer_accountant')
            return $controller->callAction('redirect', $parameters = array('staff'));
        
       
        
    })->middleware('buyer.verified')->name('buyer.dashboard');
    
  /*  Route::get('/', 'HomeController@index')->middleware('buyer.verified')->name('buyer.dashboard');*/

    // Login
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('buyer.login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('buyer.logout');

    // Register
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('buyer.register');
    Route::post('register', 'Auth\RegisterController@register');

    // Passwords
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('buyer.password.email');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('buyer.password.request');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('buyer.password.reset');

    // Verify
     Route::get('email/resend', 'Auth\VerificationController@resend')->name('buyer.verification.resend');
     Route::get('email/verify', 'Auth\VerificationController@show')->name('buyer.verification.notice');
     Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('buyer.verification.verify');
     
     ///About
    Route::get('about', 'AboutController@index');
    Route::get('about/product_lists', 'AboutController@product_lists');
    Route::get('about/our_sellers', 'AboutController@our_sellers');
    Route::get('about/{buyer_company_id}/seller','AboutController@show');
    Route::get('about/{buyer_company_id}/buyer','AboutController@show_buyer');
});
