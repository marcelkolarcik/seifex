<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 19-Jan-20
 * Time: 21:14
 */

namespace App\Services;


use Illuminate\Support\Facades\Log;

class SessionTimer
{
    /*Class to keep key=>value pair in session
    for a specified time in minutes */

    public static function put($key,$value)
    {
        session()->put($key,$value);
        session()->put($key.'_in',time());
    }

    public static function check($key,$minutes,$data = null)
    {

        if( session()->has($key.'_in') &&   (time() - (session()->get($key.'_in') ))  / 60 >  $minutes)
        {

            session()->pull($key);
            session()->pull($key.'_in');
            $data == 'orders' ? session()->pull(date('Y').'_stats_orders') : null;
        }
    }

}