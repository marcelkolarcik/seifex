<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 12/20/2018
 * Time: 9:26 PM
 */

namespace App\Services;
use App\Delegation;


class Role
{
    
    static function guard()
    {
        if(\Auth::guard('buyer')->user())
        {
            return 'buyer';
        }
        elseif(\Auth::guard('seller')->user())
        {
            return 'seller';
        }
        elseif(\Auth::guard('owner')->user())
        {
            return 'owner';
        }
        elseif(\Auth::guard('admin')->user())
        {
            return 'admin';
        }
    }
    
    public function get_guard()
    {
        if(\Auth::guard('buyer')->user())
        {
            return 'buyer';
        }
        elseif(\Auth::guard('seller')->user())
        {
            return 'seller';
        }
        elseif(\Auth::guard('owner')->user())
        {
            return 'owner';
        }
        elseif(\Auth::guard('admin')->user())
        {
            return 'admin';
        }
    }
    
    public function get_opposite_guard()
    {
        if(\Auth::guard('buyer')->user())
        {
            return 'seller';
        }
        elseif(\Auth::guard('seller')->user())
        {
            return 'buyer';
        }
    }
    public function get_owner_or_staff()
    {
        if(explode('_',\Auth::guard($this->get_guard())->user()->role)[1]   ==  'owner')
        {
            return 'owner';
        }
        elseif( in_array( explode('_',\Auth::guard($this->get_guard())->user()->role)[1], ['buyer','accountant','seller','delivery'])     )
        {
            return 'staff';
        }
    }
    static function owner_or_staff()
    {
        if(explode('_',\Auth::guard(Role::guard())->user()->role)[1]   ==  'owner')
        {
            return 'owner';
        }
        elseif( in_array( explode('_',\Auth::guard(Role::guard())->user()->role)[1], ['buyer','accountant','seller','delivery'])     )
        {
            return 'staff';
        }
    }
    public function days()
    {
        $days = [
            1=>__('monday'),
            2=>__('tuesday'),
            3=>__('wednesday'),
            4=>__('thursday'),
            5=>__('friday'),
            6=>__('saturday'),
            7=>__('sunday')
            ];
        
        return $days;
    }
    static function get_owner_id_s()
    {
        if(\Auth::guard('buyer')->user())
        {
            if(explode('_',\Auth::guard('buyer')->user()->role)[1]  ==  'owner')
            {
                return \Auth::guard('buyer')->user()->id;
            }
            elseif(explode('_',\Auth::guard('buyer')->user()->role)[1]  !=  'owner')
            {
                return Delegation::where('staff_email',\Auth::guard('buyer')->user()->email)->pluck('delegator_id')->last();
            }
        }
        if(\Auth::guard('seller')->user())
        {
        
            if(explode('_',\Auth::guard('seller')->user()->role)[1]  ==  'owner')
            {
                return \Auth::guard('seller')->user()->id;
            }
            elseif(explode('_',\Auth::guard('seller')->user()->role)[1]  !=  'owner')
            {
                return Delegation::where('staff_email',\Auth::guard('seller')->user()->email)->pluck('delegator_id')->last();
            }
        }
    }
    public function get_owner_id()
    {
        if(\Auth::guard('buyer')->user())
        {
            if(explode('_',\Auth::guard('buyer')->user()->role)[1]  ==  'owner')
            {
                return \Auth::guard('buyer')->user()->id;
            }
            elseif(explode('_',\Auth::guard('buyer')->user()->role)[1]  !=  'owner')
            {
                return Delegation::where('staff_email',\Auth::guard('buyer')->user()->email)->pluck('delegator_id')->last();
            }
        }
        if(\Auth::guard('seller')->user())
        {
          
            if(explode('_',\Auth::guard('seller')->user()->role)[1]  ==  'owner')
            {
                return \Auth::guard('seller')->user()->id;
            }
            elseif(explode('_',\Auth::guard('seller')->user()->role)[1]  !=  'owner')
            {
                return Delegation::where('staff_email',\Auth::guard('seller')->user()->email)->pluck('delegator_id')->last();
            }
        }
        
        
    }
    
    
    
    public  function staff_duties($company_id)
    {
    
        if(!isset(json_decode(\Auth::guard($this->get_guard())->user()->duties,true)[$company_id][\Auth::guard($this->get_guard())->user()->id][\Auth::guard($this->get_guard())->user()->role]))
        {
            
            return null ;
        }
    
        $staff_duties               =   json_decode(\Auth::guard($this->get_guard())->user()->duties,true)[$company_id][\Auth::guard($this->get_guard())->user()->id][\Auth::guard($this->get_guard())->user()->role];
        return $staff_duties;
    }
    
    public  function full_role() {
        
        return  \Auth::guard($this->get_guard())->user()->role;
        
    }
    
    public  function partial_role() {
        
        return  explode('_', \Auth::guard($this->get_guard())->user()->role)[0];
        
    }
    
    public  function opposite_partial_role() {
        
        if( explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'seller')
        {
            return 'buyer';
        }
        elseif(explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'buyer')
        {
            return 'seller';
        }
       
        
    }
    
    public  function position() {
        
        return  explode('_', \Auth::guard($this->get_guard())->user()->role)[1];
        
    }
    public function model_table() {
        
        return  explode('_', \Auth::guard($this->get_guard())->user()->role)[0].'s';
        
    }
    public function company_table() {
       
        return  explode('_', \Auth::guard($this->get_guard())->user()->role)[0].'_companies';
        
    }
    public  function opposite_company_table() {
    
        if( explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'seller')
        {
            return 'buyer_companies';
        }
        elseif(explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'buyer')
        {
            return 'seller_companies';
        }
    }
    
    public  function current_company_model()
    {
        if( explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'seller')
        {
            return 'App\SellerCompany';
        }
        elseif(explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'buyer')
        {
            return  'App\BuyerCompany';
        }
    }
    
    public  function opposite_company_model()
    {
        if( explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'seller')
        {
            return  'App\BuyerCompany';
        }
        elseif(explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'buyer')
        {
            return 'App\SellerCompany';
        }
    }
    
    public  function owner_id() {
        
        return  explode('_', \Auth::guard($this->get_guard())->user()->role)[0].'_id';
        
    }
    
    public  function company_id() {
        
        return  explode('_', \Auth::guard($this->get_guard())->user()->role)[0].'_company_id';
        
    }
    
    public  function company_name() {
        
        return  explode('_', \Auth::guard($this->get_guard())->user()->role)[0].'_company_name';
        
    }
    
    public  function opposite_company_name() {
        
        if( explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'seller')
        {
            return 'buyer_company_name';
        }
        elseif(explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'buyer')
        {
            return 'seller_company_name';
        }
        
    }
    
    public  function opposite_company_id() {
        
        if( explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'seller')
        {
            return 'buyer_company_id';
        }
        elseif(explode('_', \Auth::guard($this->get_guard())->user()->role)[0] == 'buyer')
        {
            return 'seller_company_id';
        }
        
       
        
    }
}
