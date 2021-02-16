<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Duty;
use App\Http\Requests\DutyRequest;
use App\Services\Role;


class DutyController extends Controller
{
    public $role;
    public function __construct(Role $role)
    {
        /*$this->middleware('admin.auth:admin');*/
        $this->role = $role;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $guard   =   $this->role->get_guard();
       $duties     =   Duty::get(['role','duty_name','duty_description','id','duty_for','lead_duty'])
            ->groupBy(['role','duty_for'])
            ->sortByDesc('lead_duty')
            ->toArray();
        $roles      =   $this->roles();
   
        return view('staff_duties.index',compact('roles','duties','guard'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */#
    public function create()
    {
        $guard   =   $this->role->get_guard();
        $roles      =   $this->roles();
        return view('staff_duties.create',compact('roles','guard'));
    
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
       
         
            $Duty =  \Auth::guard($this->role->get_guard())->user()->staff_duties()->create($request->all());
            
            return $Duty;
        
        
        
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
        $guard   =   $this->role->get_guard();
        $duty = Duty::where('id','=',$id)/*->Owners()*/->first();
        return view('staff_duties.edit', compact('duty','guard'));
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
