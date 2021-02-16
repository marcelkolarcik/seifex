<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use \Illuminate\Support\Facades\Gate;
use App\Broadcasting\BuyerNotificationChannel;
use App\Broadcasting\SellerNotificationChannel;


 Broadcast::channel('BuyerNotifications',BuyerNotificationChannel::class, ['guards' => ['buyer']]);
 
 Broadcast::channel('SellerNotifications',SellerNotificationChannel::class, ['guards' => ['seller']]);
 
 

























//////////////////////  ORDERS

/////// BUYER IS PLACING ORDER WITH SELLER => SELLER IS LISTENING ON orderPlaced CHANNEL
//
//Broadcast::channel('orderPlaced', function($user) {
//
//    return Gate::forUser(\Auth::guard('seller')->user())->allows('seller-see-orders');
//
//}, ['guards' => ['seller']]);
//
////// SELLER IS DISPATCHING ORDER => BUYER IS LISTENING ON orderUpdates
//
//Broadcast::channel('orderUpdates', function($user) {
//
//    return Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-see-orders');
//
//}, ['guards' => ['buyer']]);
//
/////// BUYER IS CONFIRMING ORDER WITH SELLER => SELLER IS LISTENING ON orderConfirmed CHANNEL
//
//Broadcast::channel('orderConfirmed', function($user) {
//
//    return Gate::forUser(\Auth::guard('seller')->user())->allows('seller-see-orders');
//
//}, ['guards' => ['seller']]);


//////////////////// INVOICES

Broadcast::channel('invoiceSent', function($user) {
    
    return Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-see-invoices');
    
}, ['guards' => ['buyer']]);

Broadcast::channel('invoicePaid', function($user) {
    
    return Gate::forUser(\Auth::guard('seller')->user())->allows('seller-see-invoices');
    
}, ['guards' => ['seller']]);

Broadcast::channel('invoiceConfirmed', function($user) {
    
    return Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-see-invoices');
    
}, ['guards' => ['buyer']]);

/////   PRODUCT LIST REQUESTS BETWEEN BUYER AND SELLER

Broadcast::channel('ProductList', function($user) {
    
    if(\Auth::guard('buyer')->user())
        return Gate::forUser(\Auth::guard('buyer')->user())->allows('buyer-coordinate-requests');
    
    if(\Auth::guard('seller')->user())
        return  Gate::forUser(\Auth::guard('seller')->user())->allows('seller-coordinate-requests');
    
}, ['guards' => ['buyer','seller']]);

/////   COMPANY ACTIVATION

Broadcast::channel('CompanyActivation', function($user) {
    
    if(\Auth::guard('buyer')->user())
        return Gate::forUser(\Auth::guard('buyer')->user())->allows('de-activate-seller');
    
    if(\Auth::guard('seller')->user())
        return  Gate::forUser(\Auth::guard('seller')->user())->allows('de-activate-buyer');
    
}, ['guards' => ['buyer','seller']]);

//// BUYER REMOVED / ADDED PRODUCT FROM / TO PRICE LIST

Broadcast::channel('ProductMoved', function($user) {
  
  return  Gate::forUser(\Auth::guard('seller')->user())->allows('price-product-list');
  
}, ['guards' => ['seller']]);

///////   PAYMENT FREQUENCY CHANGED//// SELLER IS CHANGING PAYMENT FREQUENCY => BUYER IS LISTENING ON PaymentFrequency

Broadcast::channel('PaymentFrequency', function($user) {
    
    if(\Auth::guard('buyer')->user() ) return true;
    
}, ['guards' => ['buyer']]);

///// DELIVERY DAYS CHANGED BY SELLER, BUYER IS LISTENING
///
Broadcast::channel('DeliveryDays', function($user) {
    
    if(\Auth::guard('buyer')->user() ) return true;
    
}, ['guards' => ['buyer']]);


