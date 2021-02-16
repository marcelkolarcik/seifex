<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkScope extends Model
{
    protected $fillable = [
        'details','delegation_id','guard','staff_id'
    ];
}
