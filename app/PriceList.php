<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'price_list','buyer_company_id','seller_company_id','department'
    ];
}
