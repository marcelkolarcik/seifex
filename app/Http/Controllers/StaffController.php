<?php

namespace App\Http\Controllers;

use App\Events\BuyerNotificationEvent;
use App\Events\SellerNotificationEvent;
use App\Services\Departments;
use App\Services\Language;
use App\Services\LocationNameOrId;
use Illuminate\Http\Request;
use App\Repository\NavigationRepository;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Services\Role;
use App\Delegation;
use App\Buyer;
use App\Seller;
use App\Duty;
use App\Repository\DelegationRepository;
use App\Services\DeliveryDays;
use App\Services\Company;


class StaffController extends Controller
{

    public $navigationRepository;
    public $role;
    public $company;
    public $delegation;
    public $delivery;

    public function __construct(NavigationRepository $navigationRepository,
                                Role $role,
                                DelegationRepository $delegation,
                                DeliveryDays $delivery,
                                Company $company)
    {

        $this->middleware('buyer_seller');
        $this->navigationRepository = $navigationRepository;
        $this->role = $role;
        $this->delegation = $delegation;
        $this->delivery = $delivery;
        $this->company = $company;

    }

    private function companies()
    {
        return $this->company->for($this->role->get_owner_or_staff());
    }

    public function staff()
    {

        $companies = $this->companies();
        $company = $companies[session()->get('company_id')];
        $staff = $company->staff;

        unset($staff['logged_in_staff']);
        unset($staff['staff_ids']);

        return view($this->role->partial_role() . '.staff.staff', compact('companies', 'company', 'staff'));
    }

    public function edit_scope(Request $request)
    {
        $staff = $this->companies()[session()->get('company_id')]->staff[$request->role][$request->staff_hash];

        $staff_details = [
            'name' => $staff['staff_name'],
            'delegation_id' => $staff['delegation_id'],
            'position' => $staff['staff_position'],
            'email' => $staff['email'],
            'phone_number' => $staff['phone_number'],
            'staff_id' => $staff['staff_id'],
        ];

        $scope = $staff['scope'];


        $delegation_id = $staff['delegation_id'];
        $staff_role = $staff['role'];

        $languages = Language::get_language_names(session()->get('company_languages'), 'key_value');
        $details = [];

        if ($staff_role == 'seller_seller' || $staff_role == 'seller_delivery') {
            /*ARRAY UNIQUE, BECAUSE THERE CAN BE SAME LOCATION MORE TIMES DUE TO MORE DEPARTMENTS */
            $details ['locations'] = array_unique(Arr::flatten($this->companies()[session()->get('company_id')]->delivery_locations, 1),
                SORT_REGULAR);


            $details['departments'] = isset($this->companies()[session()->get('company_id')]->price_lists_extended['price_lists_extended'])
                ? array_keys($this->companies()[session()->get('company_id')]->price_lists_extended['price_lists_extended'])
                : [];
            $details['languages'] = $languages;

            $last_location_ids = [];
            $base_location_ids = [];
            $new_ll = [];
            $location_names = [];
            $new_ll['last_locations'] = [];

            if ($scope !== null) {
                array_walk_recursive($scope['last_locations'], function ($item, $key) use (&$last_location_ids) {
                    $last_location_ids[] = $item;
                });

                array_walk_recursive($scope['base_locations'], function ($item, $key) use (&$base_location_ids) {
                    $base_location_ids[] = $key;
                });

                foreach ($scope['last_locations'] as $level => $data) {
                    foreach ($data as $base_locations => $bl_data) {
                        $new_ll['last_locations'][$base_locations][$level] = $bl_data;
                    }

                }

                $scope['last_locations'] = $new_ll['last_locations'];

                $location_names = LocationNameOrId::get_name_and_path(array_merge($base_location_ids, $last_location_ids));
            }


        }
        if ($staff_role == 'seller_accountant') {
            $details['companies'] = $this->companies()[session()->get('company_id')]->buyer_companies;
            $details['languages'] = $languages;
        }
        if ($staff_role == 'buyer_accountant') {
            $details['companies'] = $this->companies()[session()->get('company_id')]->seller_companies;
            $details['languages'] = $languages;
            $scope['languages'] = $staff['scope']['languages'];
            $scope['companies'] = $staff['scope']['companies'];

        }

        if ($staff_role == 'buyer_buyer') {
            $this->company->update('product_lists');
            $details['departments'] = isset($this->companies()[session()->get('company_id')]->product_lists) ?
                array_keys($this->companies()[session()->get('company_id')]->product_lists) : [];
            $details['languages'] = $languages;
            $scope['departments'] = $staff['scope']['departments'];
            $scope['languages'] = $staff['scope']['languages'];


        }

        return view('staff.edit.form', compact(
            'scope',
            'languages',
            'details',
            'delegation_id',
            'staff_role',
            'location_names',
            'staff_details'));


    }

    public function duties(Request $request)
    {

        $staff_id = $request->staff_id;
        $staff_role = $request->staff_role;
        $staff_hash = $request->staff_hash;

        $default_duties = Duty::where('role', $staff_role)
            ->get(['role', 'duty_name', 'duty_description', 'id', 'duty_for', 'lead_duty'])
            ->groupBy(['role', 'duty_for'])
            ->sortByDesc('lead_duty')
            ->toArray();

        $company = $this->companies()[session()->get('company_id')];
        // dd($company->staff);
        $staff_duties = isset($company->staff[$staff_role][$staff_hash]['duties'][session()->get('company_id')][$staff_id][$staff_role]) ?
            $company->staff[$staff_role][$staff_hash]['duties'][session()->get('company_id')][$staff_id][$staff_role]
            : [];
        $staff = $company->staff[$staff_role][$staff_hash];
        $duties = $this->staff_duties($default_duties, $staff_duties);

        return view('duties.create', compact(
            'duties',
            'staff',
            'staff_hash'

        ));
    }

    public function create_staff(Request $request)
    {

        $d_role = $request->staff_role;
        $staff_position = $request->staff_position;
        $delegation_id = $request->manager_delegation_id;
        $staff_id = $request->staff_id;
        $staff_phone_number = $request->phone_number;

        $creating_company_class = 'd-none';
        $languages = Language::get_language_names(session()->get('company_languages'), 'key_value');

//        $company = (object) ['country' => session()->get('company_country')];

        if ($d_role == 'buyer_accountant') {
            $details['companies'] = $this->company->for($this->role->get_owner_or_staff())[session()->get('company_id')]->seller_companies;
            $details['languages'] = $languages;

        }

        if ($d_role == 'buyer_buyer') {
            $details['departments'] = isset($this->company->for($this->role->get_owner_or_staff())[session()->get('company_id')]->product_lists)
                ? array_keys($this->company->for($this->role->get_owner_or_staff())[session()->get('company_id')]->product_lists)
                : [];
            $details['languages'] = $languages;

        }

        if ($d_role == 'seller_accountant') {


            $details['companies'] = $this->companies()[session()->get('company_id')]->buyer_companies;
            $details['languages'] = $languages;


        }
        if ($d_role == 'seller_seller' || $d_role == 'seller_delivery') {
            $details ['locations'] = $this->companies()[session()->get('company_id')]->delivery_locations;
            $details['languages'] = $languages;
            $details['departments'] = isset($this->companies()[session()->get('company_id')]->price_lists_extended['price_lists_extended'])
                ?
                array_keys($this->companies()[session()->get('company_id')]->price_lists_extended['price_lists_extended'])
                : [];


        }

        return view('staff.create.' . $d_role, compact('details',
            'd_role',
            'creating_company_class',
            'staff_position',
            'delegation_id'
            , 'staff_phone_number',
            'staff_id'));


    }

    public function undelegate_staff(Request $request)
    {


        $company_name_desc = $request->company_name_desc;
        $company_name = $request->company_name;
        $request->request->remove('company_name_desc');
        $request->request->remove('company_name');
        $request->request->add([$company_name_desc => $company_name]);

        $this->delegation->undelegate_staff($request, $request->staff_email, $request->staff_role, $request->staff_position);
        $this->company->update('staff');
        $details = [
            'n_link' => '/' . $this->role->get_guard(),
            'staff_email' => $request->staff_email,
            'action' => 'staff_fired',
            'guard' => $this->role->get_guard(),
            'subject' => __('Your were fired from company_name.',
                [
                    'company_name' => $company_name
                ]),
        ];
        /////    PUSHER
        $this->role->get_guard() == 'buyer'
            ?
            BuyerNotificationEvent::dispatch($details)
            :
            SellerNotificationEvent::dispatch($details);

        return ['status' => 'deleted'];
//
    }

    public function add_staff(Request $request)
    {


        $details = $this->work_scope($request);

        /*ADDING NEW STAFF */
        if ($request->staff_position === null) {

            $staff_details = [
                'owner_name' => \Auth::guard($this->role->get_guard())->user()->name,
                'staff_name' => $request->name,
                'staff_email' => $request->email,
                'company_name' => session()->get('company_name')

            ];
            /*DELEGATING NEW STAFF*/
            $delegation_id = $this->delegation->delegate_staff(
                $request,
                session()->get('company_id'),
                $request->staff_role,
                $request->email,
                'staff',
                $staff_details);

            $staff_id = null;
        } /*CREATING SCOPE FOR MANAGER , NOT DELEGATING BECAUSE MANAGER IS ALREADY IN DELEGATIONS TABLE
        OR EDITING STAFF OR MANAGER*/

        else {
            $delegation_id = $request->delegation_id;
            $staff_id = $request->staff_id;
        }

        DB::table('work_scopes')
            ->updateOrInsert(
                [
                    'staff_id' => $staff_id,
                    'delegation_id' => $delegation_id,
                    'guard' => \Auth::guard('buyer')->user() ? 'buyer' : 'seller',
                ],

                [
                    'staff_phone_number' => $request->phone_number,
                    'details' => json_encode($details),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);


        $this->company->update('staff');

        $details = [
            'n_link' => '',
            'staff_id' => $staff_id,
            'action' => 'staff_updated',
            'guard' => $this->role->get_guard(),
            'subject' => __('Your duties were updated.'),
        ];
        /////    PUSHER
        $this->role->get_guard() == 'buyer'
            ?
            BuyerNotificationEvent::dispatch($details)
            :
            SellerNotificationEvent::dispatch($details);

        return back();
    }

    public function update_duties(Request $request)
    {
        $table = $this->role->model_table();
        $company_id = session()->get('company_id');

//        ///// DUTIES FOR ALL THE COMPANIES IF APPLICABLE
        $all_duties = $this->companies()[$company_id]->staff[$request->staff_role][$request->staff_hash]['duties'];

        if ($request->duties) {
            foreach ($request->duties as $duty) {
                ///// UPDATED DUTIES FOR CURRENT COMPANY
                $updated_duties[$company_id][$request->staff_id][$request->staff_role][$duty] = 1;
            }
            ///// UPDATIG DUTIES FOR THE CURRENT COMPANY
            $all_duties[$company_id][$request->staff_id][$request->staff_role] = $updated_duties[$company_id][$request->staff_id][$request->staff_role];
        } else {
            $all_duties[$company_id][$request->staff_id][$request->staff_role] = [];
        }


        DB::table($table)->where('id', $request->staff_id)
            ->update([
                'duties' => json_encode($all_duties),
                'updated_at' => date('Y-m-d H:i:s')]);

        $this->company->update('staff');

        //// NOTIFY BUYER BY PUSHER AND RELOAD SESSION ON BUYER SIDE

        $details = [
            'n_link' => '',
            'staff_id' => $request->staff_id,
            'action' => 'staff_updated',
            'guard' => $this->role->get_guard(),
            'subject' => __('Your duties were updated.'),
        ];
        /////    PUSHER
        $this->role->get_guard() == 'buyer'
            ?
            BuyerNotificationEvent::dispatch($details)
            :
            SellerNotificationEvent::dispatch($details);

        return back()->with('staff_duty_updated', 1);

    }

    private function work_scope($request)
    {


        if ($request->staff_role == 'seller_seller' || $request->staff_role == 'seller_delivery') {
            $last_locations = [];
            $base_locations = [];
            $levels = [
                2 => 'country',
                3 => 'county',
                4 => 'county_l4'
            ];

            /* ARRAY UNIQUE ,BECAUSE THERE MIGHT BE OVERLAPPING WHEN EDDITING STAFF*/
            $base_locations_unique = $request->base_locations == null ? [] : array_unique($request->base_locations);
            foreach ($base_locations_unique as $location) {
                $base_locations[$levels[sizeof(explode('.', $location))]][$location] = 1;
            }

            $last_locations['country'] = [];
            $last_locations['county'] = [];
            $last_locations['county_l4'] = [];
            $county_l4_counties = [];
            $country_counties = [];

            $countries = $request->request->has('countries') ? $request->countries : [];
            $counties = $request->request->has('counties') ? $request->counties : [];
            $counties_l4 = $request->request->has('counties_l4') ? $request->counties_l4 : [];

            if ($counties_l4 != []) {
                foreach ($counties_l4 as $base => $counties_array) {
                    foreach ($counties_array as $county_l4) {
                        $last_locations['county_l4'][$base] [] =
                            $county_l4;
                    }
                    $last_locations['county_l4'][$base] =
                        array_unique($last_locations['county_l4'][$base]);
                }
                $county_l4_counties = array_keys($counties_l4);
            }
            if ($counties != []) {
                foreach ($counties as $base => $counties_array) {
                    if (array_diff($counties_array, $county_l4_counties) != []) {
                        $last_locations['county'][$base] =
                            array_unique(array_diff($counties_array, $county_l4_counties));
                    }
                }
                $country_counties = array_keys($counties);
            }

            $last_countries = array_diff(array_keys($countries), $country_counties);
            $last_countries2 = array_diff(array_keys($countries), array_keys($last_locations['county_l4']));

            if ($last_countries != []) {
                foreach ($last_countries as $country) {

                    if (isset($last_locations['county'][$country])) {
                        unset($last_locations['county'][$country]);
                    }
                    if (isset($last_locations['county_l4'][$country])) {
                        unset($last_locations['county_l4'][$country]);
                    }
                    $last_locations['country'][$country][] = $country;
                }
            } elseif ($last_countries2 != []) {
                foreach ($last_countries2 as $country) {

                    if (isset($last_locations['county'][$country])) {
                        unset($last_locations['county'][$country]);
                    }
                    if (isset($last_locations['county_l4'][$country])) {
                        unset($last_locations['county_l4'][$country]);
                    }
                    $last_locations['country'][$country][] = $country;
                }
            }

            return [
                'languages' => $request->staff_languages,
                'departments' => $request->departments,
                'last_locations' => $last_locations,
                'base_locations' => $base_locations
            ];
        }
        if ($request->staff_role == 'seller_accountant' || $request->staff_role == 'buyer_accountant') {

            return [
                'companies' => $request->companies,
                'languages' => $request->staff_languages
            ];

        }
        if ($request->staff_role == 'buyer_buyer') {

            return [
                'departments' => $request->departments,
                'languages' => $request->staff_languages
            ];

        }
    }

    private function staff_duties($default_duties, $staff_duties = null)
    {
        $duties = [];
        $leads = [];

        foreach ($default_duties as $role => $actions) {
            foreach ($actions as $action => $role_duties) {

                foreach ($role_duties as $duty) {

                    // dd(in_array($duty['duty_name'],array_keys($staff_duties)),$staff_duties,$duty['duty_name']);
                    if ($staff_duties != null && in_array($duty['duty_name'], array_keys($staff_duties))) {
                        $duties[$action][$duty['duty_name']]['duty_description'] = $duty['duty_description'];
                        $duties[$action][$duty['duty_name']]['duty_for'] = $duty['duty_for'];
                        $duties[$action][$duty['duty_name']]['lead_duty'] = $duty['lead_duty'];
                        $duty['lead_duty'] === 1 ? $leads[$action][$duty['duty_for']]['lead_active'] = 1 : '';
                        $duties[$action][$duty['duty_name']]['active'] = 1;
                    } else {
                        $duties[$action][$duty['duty_name']]['duty_description'] = $duty['duty_description'];
                        $duties[$action][$duty['duty_name']]['duty_for'] = $duty['duty_for'];
                        $duties[$action][$duty['duty_name']]['lead_duty'] = $duty['lead_duty'];
                        $duties[$action][$duty['duty_name']]['active'] = 0;
                        if ($duty['lead_duty'] != 1 && !isset($leads[$action][$duty['duty_for']]['lead_active'])) {
                            $duties[$action][$duty['duty_name']]['disabled'] = 1;
                        }

                    }

                }
            }
        }

        return $duties;
    }
}
