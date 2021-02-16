<?php

namespace App\Http\Controllers\Buyer;


use App\BuyerCompany;
use App\Http\Controllers\Controller;
use App\Services\Company;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public $company;

    public function __construct(Company $company)

    {
        $this->company = $company;
        $this->middleware('buyer.auth:buyer');
    }

    public function redirect($role)
    {
        /*IF OWNER LOGS IN FOR THE FIRST TIME AND HE HAS NO COMPANY YET
        WE WILL REDIRECT TO  CREATE COMPANY PAGE*/
        if ($role == 'owner' && !BuyerCompany::where('buyer_id', Auth::guard('buyer')->user()->id)->first()) {
            return redirect()->action(
                'Buyer\CompanyController@create',
                ['first' => true]
            );
        } /*OWNER HAS AT LEAST ONE COMPANY*/
        elseif ($role == 'owner') {
            $companies = $this->company->for($role);
            if (sizeof($companies) == 1) session()->put('company_id', array_key_first($companies));
            return view('buyer.owner.dashboard', compact('companies'));
        } elseif ($role == 'staff') {
            $companies = $this->company->for($role);

            /*STAFF HAS NO COMPANY TO WORK FOR*/
            if ($companies == []) {
                return view('includes.no_active_buyer_companies');
            }

            /*STAFF HAS COMPANY TO WORK FOR*/
            return view('buyer.owner.dashboard', compact('companies'));
        }
    }

}
