<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kolarcik
 * Date: 29-Sep-19
 * Time: 22:05
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;
use App\Services\Company;
use App\Services\Role;

class Departments
{
    public $company;
    public $role;
    
    public function __construct(Company $company, Role $role)
    {
        $this->company  =   $company;
        $this->role     =   $role;
    }
    private function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }
    public function for_sc($sc_id)
    {
        $seller_company = $this->companies()[$sc_id];
    
        if( $seller_company->price_lists_extended['price_lists_extended'] == null  ||
            array_keys( $seller_company->price_lists_extended['price_lists_extended']  ) == [] )  return [];
        foreach(array_keys( $seller_company->price_lists_extended['price_lists_extended']) as $data => $dep)
        {
            $translated_dep[]   =   __(str_replace('_',' ',$dep));
        }
   
        return   array_combine(array_keys( $seller_company->price_lists_extended['price_lists_extended']), $translated_dep);
        
        
    }
    public function for_bc($bc_id)
    {
        $buyer_company = $this->companies()[$bc_id];
        $companyDepartments = array_keys($buyer_company->product_lists) ;
       
        foreach($companyDepartments as $dep)
        {
            $translated_dep[]   =   __(str_replace('_',' ',$dep));
        }
       
        return array_combine($companyDepartments, $translated_dep);
    }
    public function default_departments()
    {
        $departments =DB::table('default_departments')->pluck('department')->toArray();
        foreach($departments as $dep)
        {
            $translated_dep[]   =   __(str_replace('_',' ',$dep));
        }
    
        return array_combine($departments, $translated_dep);
    }
}
