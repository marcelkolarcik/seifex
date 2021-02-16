<?php

namespace App\Http\Controllers;

use App\Repository\StatsRepository;
use App\Services\FiguresManager;
use App\Services\GraphMaker;
use App\Services\GraphManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Services\Role;
use App\Services\Company;

class StatisticsController extends Controller
{
    public $role;
    public $company;
    public function __construct( Role $role, Company $company )
    {
        $this->middleware('buyer_seller');
        $this->role =   $role;
        $this->company =   $company;
    }
    public function company()
    {
        return  $this->company->for($this->role->get_owner_or_staff())[session()->get('company_id')];
    }
    public function default_chart()
    {
        $guard = $this->role->get_guard();
        $opposite_guard  =   $this->role->get_opposite_guard();
        $company_ = $this->company();
        $type = 'by_departments';
        $year = date('Y');
        $details = GraphManager::graph($type,$guard,$opposite_guard,$company_,$year,GraphMaker::max_graph());
       
        $years = DB::table('orders')->select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year','year')->toArray();
        arsort($years);
        for($e=1;$e<21;$e++)
        {
            if($e % 5 == 0)
            $top_products [$e]  =  $e;
        }
        
     // dd($top_products);
      //  $years = array_combine($years,$years);
        session()->put('years'.session()->get('company_id'),$years);
        
        
        return view('statistics.chart',compact('details','guard','years','top_products'));
    }
    public function chart(Request $request)
    {
       
        $guard = $this->role->get_guard();
        $opposite_guard  =   $this->role->get_opposite_guard();
        $company_ = $this->company();
        $type = $request->stats_by;
        $year = $request->period ? date('Y') :   $request->year;
        $top_products = $request->top_products ? $request->top_products :  GraphMaker::max_graph();
        $period = $request->period ? $request->period :  '';
        $details =  GraphManager::graph($type,$guard,$opposite_guard,$company_,$year,$top_products,$period) ;
    
        /*laravel double encodes graph on update, so we need to check if it is array, if not decode to array*/
        $details =   is_array($details) ? $details : json_decode($details,true);
       /*here control size of percentage displayed, if more then 5 splice it and add button more results
       $details[''more_percentage'] = true*/
        $display = 10;
        if(sizeof($details['percentage']) >= $display)
        {
            $details['percentage'] =  array_splice($details['percentage'],0,$display-1);
            $details['more_percentage'] = true;
          
        }
        
        return $details ;
       
       
    }
   public function figures(Request $request)
   {
       $guard = $this->role->get_guard();
       $opposite_guard  =   $this->role->get_opposite_guard();
       $company_ = $this->company();
       $type = $request->stats_by;
       $year = $request->year;
       $top_products = $request->top_products /*? $request->top_products : $default = 3*/;
       
       return FiguresManager::figures($type,$guard,$opposite_guard,$company_,$year,$top_products);
   }
}
