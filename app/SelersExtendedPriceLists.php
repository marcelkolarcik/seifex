<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelersExtendedPriceLists extends Model
{
    protected $fillable = [
        'price_list','seller_company_id','department'
    ];
}
