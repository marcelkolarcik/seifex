<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 03-Sep-19
 * Time: 9:48
 */

namespace App\Services;


class DutyCheckAdmin
{
    function admin($admin,$ability)
    {
        if(\Auth::guard('owner')->check()) return true;
       
        $duties   =   json_decode($admin->duties,true);
        if(!$duties) return false;
        return in_array($ability,$duties);
        
    }
    
}
