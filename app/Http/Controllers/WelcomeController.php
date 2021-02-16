<?php

namespace App\Http\Controllers;

use App\Jobs\NewCountryRequestEmailJob;
use DB;
use Illuminate\Http\Request;


class WelcomeController extends Controller
{
    public function study()
    {
        $DB_data = [

            [51.802614, -8.54399, "cork"],
            [51.90314, -8.464399, "cork"],
            [51.94614, -8.44359, "cork"],
            [51.905614, -8.4699, "cork"],
            [51.916614, -8.437399, "cork"],
            [51.973614, -8.460399, "cork"],
            [51.98664, -8.38999, "cork"],
            [51.99614, -8.4688499, "cork"],
            [51.98614, -8.66799, "cork"],
            [51.93714, -8.43699, "cork"],
            [51.906414, -8.455399, "cork"],
            [51.905614, -8.464699, "cork"],
            [51.94614, -8.46399, "cork"],
            [51.933614, -8.428799, "cork"],
            [51.9214, -8.4714399, "cork"],

            [53.10140, -6.16155, "dublin"],
            [53.20140, -6.26155, "dublin"],
            [53.30140, -6.36155, "dublin"],
            [53.40140, -6.4155, "dublin"],
            [53.50140, -6.56155, "dublin"],
            [53.60140, -6.66155, "dublin"],
            [53.750140, -6.76155, "dublin"],
            [53.80140, -6.86155, "dublin"],
            [53.90140, -6.96155, "dublin"],
            [53.351140, -6.26155, "dublin"],
            [53.3240, -6.26255, "dublin"],
            [53.353140, -6.2355, "dublin"],
            [53.35440, -6.26645, "dublin"],
            [53.35540, -6.26555, "dublin"],
            [53.35640, -6.2665, "dublin"],
            [53.357140, -6.26755, "dublin"],
            [53.35840, -6.26685, "dublin"],


            [52.272962, -9.12691, "limerick"],
            [52.10962, -9.2691, "limerick"],
            [52.220962, -9.3691, "limerick"],
            [52.40962, -9.4691, "limerick"],
            [52.50962, -9.5691, "limerick"],
            [52.60962, -9.6691, "limerick"],
            [52.30962, -9.7691, "limerick"],
            [52.00962, -9.8691, "limerick"],
            [52.10962, -9.92691, "limerick"],
            [52.00962, -9.06211, "limerick"],
            [52.27162, -9.06291, "limerick"],
            [52.27262, -9.06391, "limerick"],
            [52.27032, -9.06491, "limerick"],
            [52.27462, -9.06591, "limerick"],
            [52.27052, -9.06691, "limerick"],
            [52.27096, -9.0791, "limerick"],
            [52.2772, -9.06281, "limerick"],

            [53.272962, -9.12691, "galway"],
            [54.10962, -9.2691, "galway"],
            [53.220962, -9.3691, "galway"],
            [53.40962, -9.4691, "galway"],
            [53.50962, -9.5691, "galway"],
            [53.60962, -9.6691, "galway"],
            [53.70962, -9.7691, "galway"],
            [53.30962, -9.8691, "galway"],
            [53.90962, -9.92691, "galway"],
            [53.00962, -9.06211, "galway"],
            [53.27162, -9.06291, "galway"],
            [53.27262, -9.06391, "galway"],
            [53.27032, -9.06491, "galway"],
            [53.27462, -9.06591, "galway"],
            [53.27052, -9.06691, "galway"],
            [53.27096, -9.0791, "galway"],
            [53.2772, -9.06281, "galway"],

            [52.901614, -8.46599, "cork"],
            [52.202614, -8.44399, "cork"],
            [52.90314, -8.464399, "cork"],
            [52.94614, -8.44359, "cork"],
            [52.905614, -8.4699, "cork"],
            [52.916614, -8.437399, "cork"],
            [52.973614, -8.460399, "cork"],
            [52.98664, -8.38999, "cork"],
            [52.99614, -8.4688499, "cork"],
            [52.98614, -8.66799, "cork"],
            [52.93714, -8.43699, "cork"],
            [52.906414, -8.455399, "cork"],
            [52.905614, -8.464699, "cork"],
            [52.94614, -8.46399, "cork"],
            [52.933614, -8.428799, "cork"],
            [52.9214, -8.4714399, "cork"],
        ];

        $views = ['mountain', 'sea', 'lake', 'river', 'pool', 'beach', 'forrest', 'skyline', 'fields', 'desert'];
        $week = date('W');
        while ($week < 53) {
            $input[] = $week + 1;
            $week++;
        }


        foreach ($DB_data as $key => $data) {


            $properties  [] = [
                'p_id' => $key,
                'p_address' => '',
                'p_price_per_w' => mt_rand(150, 300),
                'p_description' => 'Beautiful room with ' . $views[$key % 10] . ' view to make you smile in the morning....',
                'p_view' => $views[$key % 10],
                'lat' => $data[0],
                'lng' => $data[1],
                'board_type' => mt_rand(0, 3),
                'room_type' => mt_rand(0, 1),
                'city' => $data[2],
                'bookings' => array_rand($input, 15),
            ];


        }
        dd(json_encode($properties));
    }

    public function index()
    {

        /*ALL THIS TO CACHE*/

        $countries = DB::table('countries')
            ->where('started_at', '!=', null)
            ->get(['country_name', 'continent_name', 'seifex_country_id']);
        $number_of_countries = sizeof($countries);
        $countries = $countries->groupBy('continent_name')->toArray();


        return view('front.welcome', compact('number_of_countries', 'countries'));
    }

    public function new_country()
    {
        $countries = DB::table('countries')
            ->where('started_at', '=', null)
            ->get(['country_name', 'continent_name', 'seifex_country_id'])
            ->groupBy('continent_name')
            ->toArray();

        $new_country = 1;

        return view('front.new_country', compact('countries', 'new_country'));
    }

    public function add_new_country(Request $request)
    {

        $token = $this->token();

        $request->validate(['requester_email' => 'bail|required|unique:new_country_requests']);

        $country = explode('@', $request->country)[1];
        // Mail::to($request->requester_email)->send(new NewCountryRequestEmail($request,$token,explode('@',$request->country)[1]));
        DB::table('new_country_requests')->insert([
            'requester_email' => $request->requester_email,
            'seifex_country_id' => explode('@', $request->country)[0],
            'country_name' => explode('@', $request->country)[1],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'token' => $token
        ]);

        dispatch(new NewCountryRequestEmailJob($request->requester_email, $token, $country));

        return back()->with('request_sent', $country);


    }

    public function country_request($email, $token)
    {
        if (DB::table('new_country_requests')
            ->where('requester_email', $email)
            ->where('token', $token)
            ->update(['confirmed_at' => date('Y-m-d H:i:s')])
        )
            return view('front.thank_you');

        return view('errors.401');
    }

    private function token()
    {
        return \Str::random(60);
    }
}
