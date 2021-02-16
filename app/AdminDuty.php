<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminDuty extends Model
{


    protected $fillable = [
        'role',
        'duty_name',
        'duty_description'

    ];
}
