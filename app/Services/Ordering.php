<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 04-Sep-19
 * Time: 10:21
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;
use App\Services\Company;
use App\Services\Role;

class Ordering
{
    public $role;
    
    public $company;
    public function __construct(Company $company, Role $role)
    {
        $this->company  =   $company;
        $this->role  =   $role;
    }
    private function buyer_companies()
    {
      return  $this->company->for($this->role->get_owner_or_staff());
    }
    public function form_details($seller_price_lists,$department,$buyer_company_id)
    {
      
        foreach($seller_price_lists as $id   =>  $price_list)
        {
            foreach ($price_list as $hash_name  =>  $data)
            {
                $price_lists[$id][$data['product_name']] =   $data;
                $translator[$id][$data['product_name']] =   $hash_name;
            }
        }
        
        $seller_extended_price_list = [];
      
        foreach($this->buyer_companies()[$buyer_company_id]->seller_price_lists_extended as $sc_id   =>  $d_price_lists)
        {
            if(in_array($sc_id, array_keys($price_lists)))
            {
    
                $seller_extended_price_list[$sc_id] = $this->buyer_companies()[$buyer_company_id]->seller_price_lists_extended[$sc_id][$department];
            }
        }
      //  dd($price_lists,$translator,$seller_extended_price_list);
        $priced_products=[];
        $unavailable_products=[];
        $prices_with_seller_id=[];
       
        foreach($price_lists as $id   =>  $list)
        {
           
            foreach($list as $product=>$data)
                {
                    ////  IF SELLER HAS PRODUCT
                    if(isset($seller_extended_price_list[$id][$translator[$id][$product]]))
                    {
                        $strange[$product][] = [$translator[$id][$product]];
                        ////// CHECK IF SELLER HAS ENOUGH PRODUCT STOCK
                        if( $seller_extended_price_list[$id][$translator[$id][$product]]['stock_level']
                            >
                            $seller_extended_price_list[$id][$translator[$id][$product]]['low_stock'])
                        {
                           
                            //// IF THE PRODUCT IS NOT UNSET BY BUYER AND  SELLER HAS PRICE FOR IT
                            if($list[$product]['price_per_kg']  >   0   &&   $list[$product]['unset']   ==  0  )
                            {
                               $price = $list[$product]['price_per_kg'];
                                $prices_with_seller_id[$product][ $id ]  =  $price;
                                $priced_products[]  =   $product;
                               
                            }
                            
                        }
                        else
                        {
                           
                            /*CATCHING UNAVAILABLE PRODUCTS FROM EACH SELLER*/
                            in_array($product,$priced_products)  ? :
                            $unavailable_products[$id]  =   $product;
                        }
                    }
                    /*CATCHING UNAVAILABLE PRODUCTS FROM EACH SELLER*/
                   
                    in_array($product,$priced_products)  ? :
                    $unavailable_products[$id]  =   $product;
                }
        }
    
      
        foreach($prices_with_seller_id as $product  =>$prices)
        {
          
            foreach($prices as $sc_id   =>  $price)
            {
              //  dd($prices_with_seller_id,$product,$prices,$sc_id,$price);
                $seller_id  =   $sc_id;
               
                $ch_products [$product]['sc_id']            =   $seller_id;
                $ch_products[$product]['hash_name']         =   $translator[$seller_id][$product];
                $ch_products [$product]['price_per_kg']     =   min(array_values($prices));
                $ch_products[$product]['box_size']          =   $seller_extended_price_list[$seller_id][$translator[$seller_id][$product]]['box_size'];
                $ch_products [$product]['additional_info']  =   $seller_extended_price_list[$seller_id][$translator[$seller_id][$product]]['additional_info'];
                $ch_products [$product]['type_brand']       =  $seller_extended_price_list[$seller_id][$translator[$seller_id][$product]]['type_brand'];
                $ch_products [$product]['product_code']     =  $seller_extended_price_list[$seller_id][$translator[$seller_id][$product]]['product_code'];
               
            }
        }
    
        //dd($ch_products);
        return[ 'unavailable_products' => $unavailable_products, 'cheapest_products'    => $ch_products ];
    }
    
//    public function order_details($buyer_company_id,$department)
//    {
//        $price_lists =  DB::table('price_lists')
//            ->where( 'buyer_company_id', $buyer_company_id)
//            ->where('activated_by_buyer',1)
//            ->where('activated_by_seller',1)
//            ->where('department',$department)
//            ->where('currency', array_key_first(session()->get('order_currency')))
//            ->where('language',session()->get('order_language'))
//            ->pluck('price_list','seller_company_id')
//            ->toArray();
//        $new_grouped    =   [];
//        $product_prices =   [];
//
//        dd('order_details');
//
//        foreach($price_lists as $s_c_id  =>  $p_l)
//        {
//            if(json_decode($p_l,true) !== []) /////  IF SELLER HAS PRICES FOR THE BUYER
//                $new_seller_price_list[$s_c_id] =   json_decode($p_l,true);
//        }
//
//        foreach($new_seller_price_list as $id   =>  $list)
//        {
//            foreach($list as $product=>$data)
//            {
//                $new_grouped[$product] [$id]     =    $list[$product] ;
//                $product_prices  [$product] []   =    $list[$product]['price_per_kg'] ;
//            }
//        }
//
//        foreach($new_grouped as $product_a=>$prices)
//        {
//            foreach($prices as  $seller_company_id => $price)
//            {
//                if( $new_grouped[$product_a][$seller_company_id]['price_per_kg'] == min($product_prices[$product_a]))
//                {
//                    $product  =   str_replace(' ','_',$product_a);
//                    $products[$product]['product_name'] =$new_grouped[$product_a][$seller_company_id]['product_name'];
//                    $products[$product]['product_code'] =$new_grouped[$product_a][$seller_company_id]['product_code'];
//                    $products[$product]['price_per_kg'] =min($product_prices[$product_a]);
//                    $products[$product]['box_size'] =$new_grouped[$product_a][$seller_company_id]['box_size'];
//                    $products[$product]['type_brand'] =$new_grouped[$product_a][$seller_company_id]['type_brand'];
//                    $products[$product]['additional_info'] =$new_grouped[$product_a][$seller_company_id]['additional_info'];
//                    $products[$product]['box_price'] =$new_grouped[$product_a][$seller_company_id]['box_price'];
//                    $products[$product]['seller_company_id'] =$seller_company_id;
//                }
//            }
//        }
//
//        return ['products' =>  $products];
//    }
}
