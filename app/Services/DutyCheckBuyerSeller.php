<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 03-Sep-19
 * Time: 9:48
 */

namespace App\Services;


class DutyCheckBuyerSeller
{
    function buyer($buyer,$ability)
    {
        if($buyer->role  === 'buyer_owner') return true;
        
        $duties   =   json_decode($buyer->duties,true);
        
        return isset($duties[session('company_id')][$buyer->id][$buyer->role][$ability]);
        
    }
    function seller($seller,$ability)
    {
        if($seller->role  === 'seller_owner') return true;
        
        $duties   =   json_decode($seller->duties,true);
       
        return isset($duties[session('company_id')][$seller->id][$seller->role][$ability]);
        
    }
}
