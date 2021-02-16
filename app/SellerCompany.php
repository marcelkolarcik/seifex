<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerCompany extends Model
{
    
   /* protected $table = 'seller_company';*/
   
    protected $fillable = [
        'seller_id',
        'seller_owner_name',
        'seller_owner_email',
        'seller_owner_phone_number',
    
        'seller_name',
        'seller_email',
        'seller_phone_number',
    
        'seller_accountant_name',
        'seller_accountant_email',
        'seller_accountant_phone_number',
    
        'seller_delivery_name',
        'seller_delivery_email',
        'seller_delivery_phone_number',
        
        'seller_company_name',
        'address',
        'VAT_number',
        
        'country',
        'county',
        'county_l4',
       
        'last_order_at',
        'payment_method',
        'delivery_days',
        
        'currencies',
        'languages'
        
    ];
    
}


