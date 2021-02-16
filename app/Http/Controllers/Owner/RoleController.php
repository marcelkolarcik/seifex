<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
   public function index()
   {
       $roles =  $this->roles();
       $roles_active = 'active';
       
       return view('owner.roles.index', compact('roles','roles_active'));
   }
   
   public function create()
   {
       $guards  =   ['buyer'=>'buyer','seller'=>'seller'];
       $create_role_active = 'active';
       
       return view('owner.roles.create', compact('guards','create_role_active'));
   }
   
   public function store(Request $request)
   {
       DB::table('roles')
           ->insert([
               'name'=>$request->name,
               'guard'=>$request->guard,
               'creator_id'=>\Auth::guard('owner')->user()->id,
               'created_at'    => date('Y-m-d H:i:s'),
               'updated_at'    => date('Y-m-d H:i:s')
               
           ]);
      
    
       return redirect()->action(
           'Owner\RoleController@index'
       );
   }
    
   
    public function destroy($id)
    {
        DB::table('roles')
            ->where('id',$id)
            ->delete();
        
        return back();
    }
    private function roles()
    {
       
        return  DB::table('roles')->get(['guard','name','updated_at','id']);
    
       
    }
}
