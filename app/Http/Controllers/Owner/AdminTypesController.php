<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AdminTypesController extends Controller
{
    
    public function __construct(  )
    {
        $this->middleware('owner.auth:owner');
    }
    public function create()
    {
        $admin_types                =   DB::table('admin_types')->get(['admin_type','id'])->toArray();
        $create_admin_types_active  =   'active';
        return view ('owner.admins.create_admin_types',compact('admin_types','create_admin_types_active'));
    }
    
    public function store(Request $request)
    {
        DB::table('admin_types')
        ->insert([
            'admin_type'    =>  'admin_'.$request->admin_type,
            'creator_id'    =>  \Auth::guard('owner')->user()->id,
           
            
        ]);
        return back();
        
    }
    public function destroy($id)
    {
        DB::table('admin_types')->where('id',$id)->delete();
        
        return back();
    }
}
