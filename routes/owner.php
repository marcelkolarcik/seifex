<?php

Route::group(['namespace' => 'Owner'], function() {

    Route::get('/', 'DashboardController@index')->middleware('owner.verified')->name('owner.dashboard');

    // Login
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('owner.login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('owner.logout');

    // Register
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('owner.register');
    Route::post('register', 'Auth\RegisterController@register');

    // Passwords
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('owner.password.email');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('owner.password.request');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('owner.password.reset');

    // Verify
     Route::get('email/resend', 'Auth\VerificationController@resend')->name('owner.verification.resend');
     Route::get('email/verify', 'Auth\VerificationController@show')->name('owner.verification.notice');
     Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('owner.verification.verify');
     
     //Admins
    Route::get('/admins','AdminController@index');
    Route::get('/create_admin','AdminController@create');
    Route::post('/create_admin','AdminController@store');
    Route::get('/admin/assign/{id}', 'AdminController@show_admin_duties');
    Route::post('/admin/assign/{id}', 'AdminController@assign_admin_duties');
    Route::post('/admin/deactivate/{id}', 'AdminController@deactivate_admin');
    Route::post('/admin/activate/{id}', 'AdminController@activate_admin');
    
    Route::get('/create_admin_types','AdminTypesController@create');
    Route::post('/create_admin_types','AdminTypesController@store');
    Route::get('/delete_admin_type/{id}','AdminTypesController@destroy');
    
    Route::get('/create_admin_duty', 'AdminDutiesController@create');
    Route::post('/create_admin_duty', 'AdminDutiesController@store');
    Route::get('edit_duty/{duty_id}', 'AdminDutiesController@edit');
    Route::match(['put', 'patch'],'admin_duty/{id}', 'AdminDutiesController@update');
    Route::delete('delete_admin_duty/{id}', 'AdminDutiesController@destroy');
    
   
    /// Countries
    
    Route::get('/countries','CountryController@current');
    Route::get('/add_country','CountryController@add_country');
    Route::post('/add_country','CountryController@store');
    Route::get('remove_country','CountryController@remove_country');
    Route::post('remove_country','CountryController@remove_country_post');
    Route::get('new_requests','CountryController@new_requests');
    
    //// Statistics
    Route::get('/statistics','StatisticsController@index');
    Route::get('/statistics/{type}','StatisticsController@countries');
    Route::get('/statistics/{type}/{country}','StatisticsController@country');
    Route::get('/statistics/{type}/{country}/{company}','StatisticsController@company');
    
    /////Seifex Income
    
     Route::get('/income','SeifexIncomeController@index');
    
    //// Roles
    Route::get('/roles','RoleController@index');
    Route::get('/create_role','RoleController@create');
    Route::post('/create_role','RoleController@store');
    Route::get('/edit_role/{id}','RoleController@edit');
    Route::match(['put', 'patch'],'/edit_role/{id}','RoleController@update');
    Route::delete('delete_role/{id}','RoleController@destroy');
    
   ////// Backup
    Route::get('/backup','DashboardController@make_backup');
    Route::post('/backup','DashboardController@backup');
});
