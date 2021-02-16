<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests\AdminDutyRequest;
use App\AdminDuty;

class AdminDutiesController extends Controller
{
    
    public function __construct(  )
    {
        $this->middleware('owner.auth:owner');
    }
    public function create()
    {
        $admin_types                 =   DB::table('admin_types')->pluck('admin_type')->toArray();
        $admin_types                 =   (array_combine($admin_types,$admin_types));
        $admin_duties                =   DB::table('admin_duties')->get(['id','role','duty_name','duty_description'])->groupBy('role')->toArray();
        
        $create_admin_duties_active  =   'active';
        
        return view ('owner.admins.create_admin_duty',compact('admin_types','create_admin_duties_active','admin_duties'));
    }
    
    public function store(AdminDutyRequest $request)
    {
        $this->createDuty($request);
        return back();
    }
    private function createDuty(AdminDutyRequest $request)
    {
        $Duty =  \Auth::guard('owner')->user()->duties()->create($request->all());
        return $Duty;
        
        
    }
    public function edit($id)
    {
        $duty = AdminDuty::where('id','=',$id)->where('owner_id','=', \Auth::guard('owner')->user()->id)->first();
        return view('owner.admins.edit_admin_duty', compact('duty'));
    }
    
    public function update(AdminDutyRequest $request,$id)
    {
        $duty = $request->all();
        
        unset($duty['_token']);
        unset($duty['_method']);
        
        AdminDuty::where('id','=',$id)->where('owner_id','=', \Auth::guard('owner')->user()->id)->update($duty);
    
        return redirect('/owner/create_admin_duty');
    }
    public function destroy($id)
    {
        DB::table('admin_duties')->where('id',$id)->delete();
    
        return redirect('/owner/create_admin_duty');
    }
}
