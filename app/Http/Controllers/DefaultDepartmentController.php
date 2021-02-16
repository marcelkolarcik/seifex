<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\DefaultDepartment;
use App\Http\Requests\DefaultDepartmentRequest;
use App\Services\Role;

class DefaultDepartmentController extends Controller
{
    public $role;
    public function __construct(Role $role)
    {
        $this->role =   $role;
    }
    public function index()
    {
        $default_departments = DefaultDepartment::/*Owners()->*/Undeleted()
            ->get();
        $guard   =   $this->role->get_guard();
        return view('default_departments.index', compact('default_departments','guard'));
    }
    public function show($id)
    {
        $guard   =   $this->role->get_guard();
        $defaultDepartment = DefaultDepartment::Owners()->Undeleted()->where('id','=',$id)->first();

        return view('default_departments.show', compact('defaultDepartment','guard'));
    }

    public function create()
    {
        return view('default_departments.create');
    }

    public function store(DefaultDepartmentRequest $request)
    {
        $this->createDefaultDepartment($request);
        return redirect('departments');
    }
    public function edit($id)
    {
        $guard   =   $this->role->get_guard();
        $default_department = DefaultDepartment::where('id','=',$id)/*->Owners()*/->first();
        return view('default_departments.edit', compact('default_department','guard'));
    }
    public function update(DefaultDepartmentRequest $request,$id)
    {
        $defaultDepartment = $request->all();

        unset($defaultDepartment['_token']);
        unset($defaultDepartment['_method']);

        DefaultDepartment::where('id','=',$id)->update($defaultDepartment);
    
        return redirect('departments');
    }
    public function destroy($id)
    {
        \App\DefaultDepartment::destroy($id);
        return redirect('departments');
    }
    public function soft_delete(DefaultDepartmentRequest $request,$id)
    {
        
        $defaultDepartment = $request->all();
        
        unset($defaultDepartment['_token']);
        unset($defaultDepartment['_method']);

        DefaultDepartment::where('id','=',$id)->update(['deleted','=','1']);
    
        return redirect('departments');
    }
    private function createDefaultDepartment(DefaultDepartmentRequest $request)
    {
        $DefaultDepartment =  \Auth::guard($this->role->get_guard())->user()->defaultDepartments()->create($request->all());
        return $DefaultDepartment;


    }
}
