<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Admin;

/*use App\Repository\StockListRequestsRepository;
use App\Repository\LocationIdRepository;
use App\Repository\FormsRepository;*/
class RoleController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('admin.auth:admin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        DB::table('admin_delegations')
            ->where('delegated_email',\Auth::guard('admin')->user()->email)
            ->update(['accepted_at' => date('Y-m-d H:i:s')]);
        
        $admin  =   Admin::find(\Auth::guard('admin')->user()->id);
        
        if($admin->suspended)  return view('admin.admin.suspended');
        else return view('admin.admin.dashboard',compact('admin'));
       
        
       
    }
    public function super()
    {
        dd('id of super'.\Auth::guard('admin')->user()->id);
        
    }
    public function ceo()
    {
        DB::table('admin_delegations')
            ->where('delegated_email',\Auth::guard('admin')->user()->email)
            ->update(['accepted_at' => date('Y-m-d H:i:s')]);
    
        $admin  =   Admin::find(\Auth::guard('admin')->user()->id);
       
        if($admin->suspended)  return view('admin.admin.suspended');
        else return view('admin.admin.dashboard',compact('admin'));
        
    }
    
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
