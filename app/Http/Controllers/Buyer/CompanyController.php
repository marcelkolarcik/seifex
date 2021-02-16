<?php

namespace App\Http\Controllers\Buyer;


use App\Events\BuyerNotificationEvent;
use App\Services\Language;
use App\Services\LocationNameOrId;
use App\Services\PriceList;
use App\Services\ProductList;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Http\Request;
use DB;
use App\BuyerCompany;
use App\Http\Requests\BuyerCompanyRequest;
use Illuminate\Support\Facades\Auth;
use App\Repository\DelegationRepository;
use App\Http\Controllers\Controller;
use App\Repository\NavigationRepository;
use App\Repository\CountryRepository;
use App\Services\Role;
use App\Services\Currency;
use App\Buyer;
use Illuminate\Support\Facades\Gate;
use App\Services\Company;

class CompanyController extends Controller
{
    public $productListRepository;


    public $navigationRepository;
    public $delegator;
    public $role;
    public $currency;
    public $company;
    public $countryRepository;


    public function __construct(
        Currency $currency,
        NavigationRepository $navigationRepository,
        DelegationRepository $delegator,
        Role $role,
        CountryRepository $countryRepository,
        Company $company)
    {
        $this->middleware('buyer.auth:buyer');
        $this->middleware('buyer_owner', ['except' => ['index']]);


        $this->currency = $currency;
        $this->navigationRepository = $navigationRepository;
        $this->delegator = $delegator;
        $this->role = $role;
        $this->countryRepository = $countryRepository;
        $this->company = $company;
    }

    private function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }

    public function index($company_id)
    {
        //$this->company->update('staff');
        $companies = $this->companies();

        $company = $companies[$company_id];

        session(['company_id' => $company_id]);
        session(['buyer_company_name' => $company->buyer_company_name]);
        session(['company_name' => $company->buyer_company_name]);
        session()->put('company_languages', $company->languages['all']);
        session()->put('company_preferred_lan', $company->languages['preferred']);

        if (\Auth::guard('buyer')->user()->role == 'buyer_owner') {
            return view('buyer.company.dashboard', compact('company', 'companies'));
        }

        if (\Auth::guard('buyer')->user()->role == 'buyer_buyer'
            || Auth::guard('buyer')->user()->role == 'buyer_accountant') {
            /*STAFF LOGS INTO THE COMPANY PAGE AFTER CLICKING ON ACCEPT THE JOB IN THIS COMPANY
			WE WILL UPDATE DELEGATION AS ACCEPTED*/
            if ($company->logged_in_staff['accepted_at'] == null
                && $company->logged_in_staff['staff_email'] == Auth::guard('buyer')->user()->email
                && $company->logged_in_staff['company_id'] == $company_id) {

                DB::table('delegations')
                    ->where('staff_email', \Auth::guard('buyer')->user()->email)
                    ->where('delegator_company_id', $company_id)
                    ->where('staff_role', \Auth::guard('buyer')->user()->role)
                    ->update([
                        'staff_id' => \Auth::guard('buyer')->user()->id,
                        'accepted_at' => date('Y-m-d H:i:s')]);


                /*pusher notification to owner*/

                //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE
                $details = [
                    'n_link' => '/staff',
                    'action' => 'staff_accepted_job',
                    'guard' => 'buyer',
                    'owner_email' => $company->buyer_owner_email,
                    'subject' => __('staff_name accepted position of staff_role role.',
                        [
                            'staff_name' => Auth::guard('buyer')->user()->name,
                            'position' => $company->logged_in_staff['staff_position'],
                            'staff_role' => Auth::guard('buyer')->user()->role
                        ]),
                ];
                /////    PUSHER
                BuyerNotificationEvent::dispatch($details);

                $this->company->update(['staff', 'buyer_companies']);
                $companies = $this->companies();
                $company = $companies[$company_id];
            }


            return view('buyer.staff_includes.company', compact('company', 'companies'));

        }


    }

    public function create($first = null)
    {
        session()->put('creating_company', true);
        $creating_company_class = 'd-none';
        session()->pull('selected_country');
        session()->pull('company_currencies');
        session()->pull('company_languages');
        $country_levels = LocationNameOrId::current_countries_for_select();
        $county_levels = [];
        $county_levels_4 = [];
        $location_path = '';
        return view('buyer.company.create', compact(
            'country_levels',
            'county_levels',
            'county_levels_4',
            'seifex_currencies',
            'creating_company_class',
            'location_path',
            'first'));
    }

    public function store(BuyerCompanyRequest $request)
    {

        $country_currency = $this->currency->get_country_currency($request->country);

        $preferred_currency = $request->preferred_currency;

        $buyer_company_id = $this->createCompany($request)->id;

        $this->check_staff($request);


        if ($request->buyer_owner_email != $request->buyer_email) {
            $this->delegator->delegate_staff($request, $buyer_company_id, 'buyer_buyer', $request->buyer_email, 'manager');
        }
        if ($request->buyer_owner_email != $request->buyer_accountant_email) {
            $this->delegator->delegate_staff($request, $buyer_company_id, 'buyer_accountant', $request->buyer_accountant_email, 'manager');
        }


        if ($country_currency !== $preferred_currency) {

            session()->push('buyer_company_ids', $buyer_company_id);

            return redirect('/edit_buyer_company/' . $buyer_company_id)
                ->withInput()
                ->with('form_updated', 1)
                ->with('preferred_currency', $preferred_currency)
                ->with('country_currency', __('country_currency Currency from the country you are in seems to be not selected',
                    [
                        __('country_currency') => $country_currency
                    ]));
        }
        $this->company->update(['buyer_companies', 'staff']);

        return redirect('/buyer');
    }

    public function edit($id)
    {

        session()->pull('creating_company');
        session()->pull('new_currency');


        $creating_company_class = '';
        $country_levels = LocationNameOrId::current_countries_for_select();
        $county_levels = [];
        $county_levels_4 = [];

        //  $this->company->update('buyer_companies');
        $company = $this->companies()[$id];


        session()->put('selected_country', $company->country);

        session()->put('company_currencies', $company->currencies['all']);
        session()->put('company_languages', $company->languages['all']);

        $company_currencies = Currency::add_data_to_currency($company->currencies['all']);
        $country_currency = Currency::get_country_currency($company->country);
        $missing_country_currency = in_array($country_currency, $company->currencies['all']) ? false : Currency::add_data_to_currency([$country_currency]);
        session()->put('missing_country_currency', $missing_country_currency);

        $preferred_currency = Currency::add_data_to_currency([$company->currencies['preferred']]);
        $location_path = LocationNameOrId::path([$company->country, $company->county, $company->county_l4]);
        $languages = Language::get_language_names($company->languages);
        $country_languages = Language::country_language($company->country);

        $missing_country_languages = array_diff($country_languages, $languages['all']);
        session()->put('missing_country_languages', $missing_country_languages);

        return view('buyer.company.edit', compact(
            'IDs',
            'company',
            'country_levels',
            'county_levels',
            'county_levels_4',
            'company_currencies',
            'preferred_currency',
            'languages',
            'creating_company_class',
            'location_path',
            'missing_country_languages',
            'missing_country_currency'));
    }

    public function update(BuyerCompanyRequest $request, $buyer_company_id)
    {

        $country_currency = $this->currency->get_country_currency($request->country);
        $preferred_currency = $request->preferred_currency;

        $company = $this->companies()[$buyer_company_id];

        $old_languages = $company->languages;
        $old_preferred_language = $old_languages['preferred'];

        $preferred_language = $request->preferred_language;

        if ($old_preferred_language !== $preferred_language) {
            ProductList::change_preferred_language($old_preferred_language, $preferred_language);
        }

        $company_details = $this->prepare_request($request);

        /*PRODUCT LIST  REMOVED LANGUAGES*/
        ProductList::disable_language(array_diff($old_languages['all'],
            json_decode($company_details['languages'], true)['all']));

        /*PRODUCT LIST  ADDED LANGUAGES*/
        ProductList::enable_language(array_diff(json_decode($company_details['languages'], true)['all'],
            $old_languages['all']));


        /*PRICE LIST REMOVED CURRENCIES*/
        PriceList::disable_currency(array_diff($company->currencies['all'],
            json_decode($company_details['currencies'], true)['all']));

        /*RICE LIST ADDED CURRENCIES*/
        PriceList::enable_currency(array_diff(json_decode($company_details['currencies'], true)['all'],
            $company->currencies['all']));


        /*PRICE LIST REMOVED LANGUAGES*/
        PriceList::disable_language(array_diff($old_languages['all'],
            json_decode($company_details['languages'], true)['all']));

        /*PRICE LIST ADDED LANGUAGES*/
        PriceList::enable_language(array_diff(json_decode($company_details['languages'], true)['all'],
            $old_languages['all']));


        $this->check_staff($request);

        //// IF IT IS DIFFERENT BUYER AND IT'S NOT OWNER

        if ($request->buyer_email != $company->buyer_email && $request->buyer_email != $company->buyer_owner_email) {
            $this->delegator->delegate_staff($request, $buyer_company_id, 'buyer_buyer', $request->buyer_email, 'manager');

            $this->delegator->undelegate_staff($request, $company->buyer_email, 'buyer_buyer', 'manager');
        }

        //// IF IT IS DIFFERENT ACCOUNTANT AND IT'S NOT OWNER

        if ($request->buyer_accountant_email != $company->buyer_accountant_email && $request->buyer_accountant_email != $company->buyer_owner_email) {
            $this->delegator->delegate_staff($request, $buyer_company_id, 'buyer_accountant', $request->buyer_accountant_email, 'manager');

            $this->delegator->undelegate_staff($request, $company->buyer_accountant_email, 'buyer_accountant', 'manager');
        }
        unset($company_details['_token']);
        unset($company_details['_method']);

        BuyerCompany::where('id', '=', $buyer_company_id)->where('buyer_id', \Auth::guard('buyer')->user()->id)->update($company_details);


        if ($company->buyer_company_name != $company_details['buyer_company_name']) {
            DB::table('delegations')
                ->where('delegator_company_id', $buyer_company_id)
                ->where('delegator_role', 'buyer_owner')
                ->update([
                    'delegator_company_name' => $company_details['buyer_company_name']
                ]);
        }


        $this->company->update(['buyer_companies', 'staff']);

        if ($country_currency !== $preferred_currency) {
            return back()
                ->with('form_updated', 1)
                ->with('country_currency', __('country_currency Currency from the country you are in seems not to be selected as your preferred currency',
                    [
                        __('country_currency') => $country_currency
                    ]));
        }

        return back()->with('form_updated', 1);
    }

    public function destroy($id)
    {
        //
    }

    private function check_staff($request)
    {
        if (Buyer::where('email', $request->buyer_email)->where('role', '!=', 'buyer_owner')->first()
            && \Auth::guard('buyer')->user()->email !== $request->buyer_email) {
            return back()->with('buyer_not_checked', __('We have ') . Buyer::where('email', $request->buyer_email)->pluck('role')->first() . __(' with this email'));
        } elseif (Buyer::where('email', $request->buyer_accountant_email)->where('role', '!=', 'buyer_owner')->first()
            && \Auth::guard('buyer')->user()->email !== $request->buyer_accountant_email) {
            return back()->with('buyer_accountant_not_checked', __('We have ') . Buyer::where('email', $request->seller_accountant_email)->pluck('role')->first() . __(' with this email'));
        } else {
            return 'ok';
        }

    }

    private function createCompany($request)
    {

        $company_details = $this->prepare_request($request);

        $Company = Auth::guard('buyer')->user()->buyer_companies()->create($company_details);

        return $Company;

    }

    private function prepare_request($request)
    {
        ////// PUT LANGUAGES TOGETHER INTO JSON
        $currency['preferred'] = $request->preferred_currency;
        $currency['all'] = $request->currencies;
        $request->request->remove('preferred_currency');
        $request->request->remove('currencies');
        $request->request->add(['currencies' => json_encode($currency)]);

        ////// PUT LANGUAGES TOGETHER INTO JSON
        $languages['preferred'] = $request->preferred_language;
        $languages['all'] = $request->languages;
        $request->request->remove('preferred_language');
        $request->request->remove('languages');
        $request->request->add(['languages' => json_encode($languages)]);

        $company_details = $request->all();

        $company_details['address'] = json_encode($company_details['address']);
        // dd($company_details);
        return $company_details;
    }

}
