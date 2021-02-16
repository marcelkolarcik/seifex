<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 28-Jul-19
 * Time: 17:00
 */

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Services\Role;

class DeliveryDays
{
    public $role;
    
    public function __construct(Role $role)
    {
        $this->role =   $role;
    }
    
    public function delivery_days($s_company,$bc_id,$department)
    {
      
        $location_delivery_days = $s_company->delivery_locations
                                    [$department]
                                    [$s_company->buyer_companies[$bc_id]
                                    ['delivery_locations']
                                    [$department]]
                                    ['delivery_days'];
    
       
        if( isset($s_company->price_lists[$bc_id][$department]['delivery_days']) )
        {
            $buyer_delivery_days =  $s_company->price_lists[$bc_id][$department]['delivery_days'];
        }
        else
        {
            $buyer_delivery_days = null;
        }
      
        if(isset($buyer_delivery_days))
        {
            $delivery_days_type =    $buyer_delivery_days === $location_delivery_days ? 'location':'buyer';
            $delivery_days = $buyer_delivery_days;
        }
        else
        {
            $delivery_days_type =   'location';
            $delivery_days = $location_delivery_days;
        }
        
        return[
            'delivery_days'     =>     $delivery_days,
            'type'              =>     $delivery_days_type
        ];
    }
    
}
