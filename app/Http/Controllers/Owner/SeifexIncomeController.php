<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SeifexIncomeController extends Controller
{
    
    public function __construct(  )
    {
        $this->middleware('owner.auth:owner');
    }
    
    
   public function index()
   {
        $income  =   DB::table('seifex_orders')->sum('order_value')*0.035;
        $income_active    =   'active';
     
       
      
       return view('owner.income.index', compact('income','income_active'));
   }
}
