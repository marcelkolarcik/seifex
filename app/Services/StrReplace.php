<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 19-Sep-19
 * Time: 16:24c
 */

namespace App\Services;


class StrReplace
{
    static function hash($location)
    {
        return str_replace(' ','-',str_replace(' # ','--',$location));
    }
    static function dash($location)
    {
        return str_replace('-',' ',str_replace('--',' | ',$location));
    }
    static function pipe($location)
    {
        return str_replace(' ','-',str_replace(' | ','--',$location));
    }
    static function dash_to_hash($location)
    {
        return str_replace('-',' ',str_replace('--',' # ',$location));
    }
    
    static function hash_to_pipe($location)
    {
        return str_replace(' # ',' | ',$location);
    }
    static function currency_underscore($currency_full_name)
    {
        return str_replace('_',' ',$currency_full_name);
    }
}
