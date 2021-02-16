<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 01-Oct-19
 * Time: 17:18
 */

namespace App\Services;


class Sanitizer
{
   
    static function do_strings( array $user_input,  $type = null)
    {
       
        /* if (preg_match("#https?://#", $input['url']) === 0) {
            $input['url'] = 'http://'.$input['url'];
        }*/
        if($type    ==   'price_list')
        {
            foreach($user_input  as   $name    =>  $data)
            {
                foreach($data as $desc  =>  $user_inp)
                {
                 
                    $string = filter_var($user_inp, FILTER_SANITIZE_STRING) == '' ?
                      null  : filter_var($user_inp, FILTER_SANITIZE_STRING)
                    ;
                    
                    $input[$name][$desc]     = $string;
                }
               
            }
        }
        else
        {
            foreach($user_input as $item)
            {
                $input[] = filter_var($item, FILTER_SANITIZE_STRING);
            }
        }
       
   
       return $input;
    }
    
}
