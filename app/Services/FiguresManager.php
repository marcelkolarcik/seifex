<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 15-Jan-20
 * Time: 22:21
 */

namespace App\Services;


class FiguresManager
{
    public static function figures($type,$guard,$opposite_guard,$company_,$year,$top_products)
    {
        return GraphManager::graph(str_replace('_figures','',$type),$guard,$opposite_guard,$company_,$year,$top_products)['percentage'];
    }
}
