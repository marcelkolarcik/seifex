<?php

namespace App\Http\Controllers;

use DB;

class DelegateController extends Controller
{


    public function delegations($email, $token)
    {
        if ($delegation = DB::table('delegations')
            ->where('token', $token)
            ->where('staff_email', $email)
            ->first()) {
            return view('includes.register_delegated', compact('delegation'));
        }

        abort(401, 'Unauthorized action.');

    }

    public function admin_delegations($email, $token)
    {

        if ($delegation = DB::table('admin_delegations')
            ->where('token', $token)
            ->where('delegated_email', $email)
            ->first()) {
            return view('includes.register_delegated', compact('delegation'));
        }

        abort(401, 'Unauthorized action.');

    }


}
