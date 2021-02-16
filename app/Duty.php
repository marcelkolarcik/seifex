<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
   
    
    protected  $table='staff_duties';
    
    protected $fillable = [
        'role',
        'duty_name',
        'duty_description',
        'duty_for',
        'lead_duty'
    ];
}
