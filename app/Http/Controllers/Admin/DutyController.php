<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Duty;
use App\Http\Requests\DutyRequest;


class DutyController extends Controller
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
    public function index()
    {
        $duties     =   Duty::get(['role','duty_name','duty_description','id','duty_for','lead_duty'])
            ->groupBy(['role','duty_for'])
            ->sortByDesc('lead_duty')
            ->toArray();
        $roles      =   $this->roles();
      // dd($staff_duties);
        return view('admin.staff_duties.index',compact('roles','duties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */#
    public function create()
    {
        $roles      =   $this->roles();
        return view('admin.staff_duties.create',compact('roles'));
    
    }
    
    private function roles()
    {
        $roles      =   DB::table('roles')->get(DB::raw('CONCAT(guard, "_", name) as role'))->pluck('role')->toArray();
        $roles      =   array_combine($roles,$roles);
        return $roles;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DutyRequest $request)
    {
        $this->createDuty($request);
        return redirect('staff_duties');
    }
    private function createDuty(DutyRequest $request)
    {
        if(\Auth::guard('admin')->user()->role == 'admin_admin')
        {
          
            $Duty =  \Auth::guard('admin')->user()->duties()->create($request->all());
            return $Duty;
        }
        
        
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
        $duty = Duty::where('id','=',$id)/*->Owners()*/->first();
        return view('admin.staff_duties.edit', compact('duty'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DutyRequest $request,$id)
    {
        $duty = $request->all();
        
        unset($duty['_token']);
        unset($duty['_method']);
        
        Duty::where('id','=',$id)->update($duty);
        
        return redirect('staff_duties');
    }
    public function destroy($id)
    {
       Duty::destroy($id);
        return redirect('staff_duties');
    }
}
