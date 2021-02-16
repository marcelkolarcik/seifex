<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultDepartment extends Model
{
    protected $fillable = [
        'department'
    ];

    public function scopeOwners($query)
    {
        $query->where('user_id','=',\Auth::user()->id);
    }
    public function scopeUndeleted($query)
    {
        $query->where('deleted','=','0');
    }

    /*public function scopeOwners($query)
    {
        $query->where('user_id','=',\Auth::user()->id);
    }*/
}
