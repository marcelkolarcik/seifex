<?php

namespace App\Http\Controllers\Seller;


use App\Http\Controllers\Controller;
use App\SellerCompany;
use App\Services\Company;
use Illuminate\Support\Facades\Auth;


class RoleController extends Controller
{
    public $company;
    
    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->middleware('seller.auth:seller');
    }
    
    public function redirect($role)
    {
        /*IF OWNER LOGS IN FOR THE FIRST TIME AND HE HAS NO COMPANY YET
		WE WILL REDIRECT TO  CREATE COMPANY PAGE*/
        if($role == 'owner' && !SellerCompany::where('seller_id',Auth::guard('seller')->user()->id)->first() )
        {
            return redirect()->action(
                'Seller\CompanyController@create',
                [ 'first' => true ]
            );
        }
        /*OWNER HAS AT LEAST ONE COMPANY*/
        elseif($role == 'owner' )
        {

            $companies        =   $this->company->for($role);

            return view('seller.owner.dashboard', compact('companies'));
        }
        elseif( $role == 'staff' )
        {
            $companies        =   $this->company->for($role);
            /*STAFF HAS NO COMPANY TO WORK FOR*/
            if($companies == [] )
            {
                return view('includes.no_active_seller_companies');
            }
            /*STAFF HAS COMPANY TO WORK FOR*/
            return view('seller.owner.dashboard', compact('companies'));
        }
    }
    
}
