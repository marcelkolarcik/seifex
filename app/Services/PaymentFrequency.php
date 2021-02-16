<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 29-Sep-19
 * Time: 23:02
 */

namespace App\Services;


class PaymentFrequency
{
    static function get()
    {
        return  [1  =>  __('daily'),    2   =>  __('weekly'),   3   =>  __('monthly')];
    }
}
