<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    protected $redirectTo = '/admin/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.auth:admin');
    }

    /**
     * Show the Admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
    
    
        DB::table('admin_delegations')
            ->where('delegated_email',\Auth::guard('admin')->user()->email)
            ->update(['accepted_at' => date('Y-m-d H:i:s')]);
        
        return view('admin.home');
    }

}
