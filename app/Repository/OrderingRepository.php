<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 30-Jul-19
 * Time: 15:26
 */

namespace App\Repository;

use App\Services\Role;
use Illuminate\Support\Facades\DB;
use App\Services\Ordering;
use App\Services\Company;

class OrderingRepository
{
   
    public $role;
    public $ordering;
    public $company;
    
    public function __construct( Role $role, Ordering $ordering, Company $company)
    {
        $this->role         =  $role;
        $this->ordering     =  $ordering;
        $this->company      =  $company;
    }
    
    public function display_online_form($department,$buyer_company_id, $seller_company_ids = [])
    {
        if($seller_company_ids === []) return ['no_sellers'];
        
        /*UPDATING PRICE LISTS TO GET LATEST PRICES*/
        $this->company->update('price_lists');
        
        $buyer_company  =   $this->company->for($this->role->get_owner_or_staff())[$buyer_company_id];
       $form_language =     session()->has('order_language') ? session()->get('order_language') :
           $buyer_company->languages['preferred'];
        $buyers_default_list = $buyer_company->product_lists[$department][$form_language];
        
        if(!isset($buyers_default_list))  return ['no_product_list'];
        //// ALL THE PRICE LISTS FOR THE BUYER COMAPNY/DEPARTMENT
        
        foreach($buyer_company->price_lists[$department] as $price_list)
        {
          
            if($price_list->activated_by_seller == 1
                && $price_list->activated_by_buyer == 1
            && in_array($price_list->seller_company_id,$seller_company_ids))
            {
              
                $seller_price_lists[$price_list->seller_company_id] = $price_list->price_list ;
            }
        }
    
        //// ALL THE SELLER COMPANIES FOR THE BUYER
        foreach($buyer_company->seller_companies as $id =>$seller_company)
        {
            if(in_array($id,$seller_company_ids))
                $seller_companies[$id] = (object)
                [
                    'seller_name'           =>      $seller_company['seller_name'],
                    'seller_email'          =>      $seller_company['seller_email'],
                    'seller_phone_number'   =>      $seller_company['seller_phone_number'],
                    'seller_company_name'   =>      $seller_company['company_name'],
                    'id'                    =>      $id,
                    'last_order_at'         =>      $seller_company['last_order_at'],
                ];
        }
        
        $form_details   =   $this->ordering->form_details($seller_price_lists,$department,$buyer_company_id);
        
        return [
            'cheap_products'            =>   $form_details['cheapest_products'],
            'unavailable_products'      =>   $form_details['unavailable_products'],
            'seller_companies'          =>   $seller_companies,
            ];
        
    }
  
}
