<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 15-Oct-19
 * Time: 17:33
 */

namespace App\Services;


class ArrayIs
{
    static function multi($array) {
        return (count($array) != count($array, 1));
    }
}
