<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyerCompany extends Model
{
    protected $fillable = [
        
        'buyer_id',
        'buyer_owner_name',
        'buyer_owner_email',
        'buyer_owner_phone_number',
        
        'buyer_name',
        'buyer_email',
        'buyer_phone_number',
        
        'buyer_accountant_name',
        'buyer_accountant_email',
        'buyer_accountant_phone_number',
        
        'buyer_company_name',
        'address',
        'VAT_number',
        
        'country',
        'county',
        'county_l4',
        
        'currencies',
        'languages'
        
        
    ];
}
