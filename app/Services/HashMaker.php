<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 19-Oct-19
 * Time: 17:49
 */

namespace App\Services;


class HashMaker
{
    static function product($product_name, $product_data,$rate)
    {
        
        return   $product_name .
           
            '+'.$product_data['product_code']
            .'-'. ($product_data['price_per_kg']+ 0)*(1/$rate) ///to keep hash name same for all currencies)
            .'-'.$product_data['type_brand']
            .'-'.($product_data['box_size']+ 0)
            .'-'.($product_data['box_price']+ 0)*(1/$rate) ///to keep hash name same for all currencies)
            .'-'.$product_data['additional_info'];
        
    }
}
