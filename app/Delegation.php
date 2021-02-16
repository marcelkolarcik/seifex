<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delegation extends Model
{
    protected $dates = [
    
        'created_at',
        'updated_at',
        'accepted_at',
        'delegated_at',
        'undelegated_at'
        
    ];
}
