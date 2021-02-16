<?php

Route::group(['namespace' => 'Admin'], function() {
    
    Route::get('/', function () {
        /*$users[] = Auth::user();
		$users[] = Auth::guard()->user();
		$users[] = Auth::guard('admin')->user();
	
		//dd($users);
	
		return view('admin.home');*/
        $app = app();
        $controller = $app->make('\App\Http\Controllers\Admin\RoleController');
        
        if(Auth::guard('admin')->user()->role   ==  'admin_admin')
            return $controller->callAction('admin', $parameters = array());
        /*if(Auth::guard('admin')->user()->role   ==  'super')
            return $controller->callAction('super', $parameters = array());
        */
        if(Auth::guard('admin')->user()->role   ==  'admin_ceo')
            return $controller->callAction('ceo', $parameters = array());
    })
        ->middleware('admin.verified')->name('admin.dashboard');
    
   // Route::get('/', 'HomeController@index')->middleware('admin.verified')->name('admin.dashboard');

    // Login
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout');

    // Register
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('admin.register');
    Route::post('register', 'Auth\RegisterController@register');

    // Passwords
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('admin.password.reset');

    // Verify
     Route::get('email/resend', 'Auth\VerificationController@resend')->name('admin.verification.resend');
     Route::get('email/verify', 'Auth\VerificationController@show')->name('admin.verification.notice');
     Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('admin.verification.verify');

});
