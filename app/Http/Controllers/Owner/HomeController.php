<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    protected $redirectTo = '/owner/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('owner.auth:owner');
    }

    /**
     * Show the Owner dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
    
      
        
        return view('owner.home');
    }

}
