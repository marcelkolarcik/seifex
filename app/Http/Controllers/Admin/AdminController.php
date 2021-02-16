<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AdminController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('admin.auth:admin');
    }
    
    public function index()
    {
        dd('aa');
        return view('admin.admin.dashboard');
    }
}
