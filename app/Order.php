<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [

    ];

    public function scopeOwners($query)
    {
        if(\Auth::user()->id)
        {
            $query->where('buyer_id','=',\Auth::user()->id);
        }
        elseif(\Auth::guard('web_seller')->user()->id)
        {
            $query->where('seller_id','=',\Auth::guard('web_seller')->user()->id);
        }

    }

}
