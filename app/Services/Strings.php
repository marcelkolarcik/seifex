<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 26-Jul-19
 * Time: 15:12
 */

namespace App\Services;


class Strings
{
    static function payment_frequency()
    {
        return [
          1 =>  __('daily'),
          2 =>  __('weekly'),
          3 =>  __('monthly')
        ];
    }
    
    static function days()
    {
        return [
            1=>__('monday'),
            2=>__('tuesday'),
            3=>__('wednesday'),
            4=>__('thursday'),
            5=>__('friday'),
            6=>__('saturday'),
            7=>__('sunday')
        ];
        
       
    }
}
