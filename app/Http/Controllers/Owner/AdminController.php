<?php

namespace App\Http\Controllers\Owner;

use App\Jobs\StaffDelegationEmailJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Mail\StaffDelegationEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Admin;

class AdminController extends Controller
{
    
    public function __construct(  )
    {
        $this->middleware('owner.auth:owner');
      
    }
    
    private function admin_types()
    {
        return DB::table('admin_types')->pluck('admin_type')->toArray();
    }
    
    private function admins()
    {
        return DB::table('admins')->get(['id','name','role','email','created_at','suspended'])->toArray();
    }
    private function delegated_admins()
    {
        return DB::table('admin_delegations')
            ->where('delegator_id',\Auth::guard('owner')->user()->id)
            ->where('accepted_at',null)
            ->get(['delegated_name','delegated_role','delegated_email','delegated_at','accepted_at','delegated_at'])->toArray();
    }
    private function admins_active()
    {
        return 'active';
    }
    public function index()
    {
        $admins             =    $this->admins();
    
        $delegated_admins   =    $this->delegated_admins();
       
        $admins_active      =   $this->admins_active();
        return view('owner.admins.admins',compact('admins','admins_active','delegated_admins'));
    }
    
    public function create()
    {
        $admin_types            =    array_combine($this->admin_types(),$this->admin_types());
        $create_admins_active   =   'active';
        $admins                 =    DB::table('admins')->get(['name','role','email','created_at'])->toArray();
       
        
        return view ('owner.admins.create_admin',compact('admin_types','admins','create_admins_active'));
    }
    private function delegation_details($request,$staff_role,$delegated)
    {
        
        $delegated_role         =   ucwords(explode('_',$staff_role)[1]);
        $owner_name             =   'Marcel Kolarcik';
        $company_name           =   'Seifex.com';
        $staff_name             =   $request->admin_name;
        $staff_email            =   $request->admin_email;
        $message                =   __('You have received this email, because you were delegated a job of job_title by company_owner in company.',
            [
                'company_owner'  =>  'Marcel Kolarcik',
                'job_title'      =>  explode('_',$staff_role)[1],
                'delegated'      =>  $delegated,
                'company'        =>  'Seifex.com'
            ]);
        
        
        return ['staff_name'     =>  $staff_name,
            'owner_name'     =>  $owner_name,
            'company_name'   =>  $company_name,
            'delegated_role' =>  $delegated_role,
            'staff_email'    =>  $staff_email,
            'message'        =>  $message];
    }
    public function store(Request $request)
    {
    
       //dd($request->request);
        $token = $this->token();
        $details = $this->delegation_details($request,$request->staff_role,'delegated');
        //Mail::to($request->admin_email)->send(new StaffDelegationEmail($request,$request->staff_role,$token));
        dispatch(new StaffDelegationEmailJob($details,$request->staff_role,$request->admin_email,$token));
        
        DB::table('admin_delegations')->insert([
            'delegator_id'              =>  \Auth::guard('owner')->user()->id,
            'delegator_role'            =>  \Auth::guard('owner')->user()->role,
            'delegator_email'           =>  \Auth::guard('owner')->user()->email,
            'delegated_email'           =>  $request->admin_email,
            'delegated_name'            =>  $request->admin_name,
            'delegated_role'            =>  $request->staff_role,
            'delegated_at'              =>  date('Y-m-d H:i:s'),
            'token'                     =>  $token,
        ]);
        
        $admins             =    $this->admins();
        $delegated_admins   =    $this->delegated_admins();
        $admins_active      =    $this->admins_active();
        
        return view('owner.admins.admins',compact('admins','admins_active','delegated_admins'));
    }
    public function show_admin_duties($id)
    {
        $admin           =   Admin::where('id',$id)->get()->first();
        $default_admin_duties   =   DB::table('admin_duties')->where('role',$admin->role)->get()->groupBy('duty_name')->toArray();
       
        return view('owner.admins.show', compact('admin','default_admin_duties'));
    }
    public function assign_admin_duties(Request $request,$id)
    {
        if($request->duties) $duties = $request->duties;
        else $duties = [];
        
        DB::table('admins')->where('id',$id)
            ->update([
                'duties'           =>  json_encode($duties),
                'updated_at'       =>  date('Y-m-d H:i:s')]);
    
        return   back()->with('admin_duty_updated',1);
    }
    public function deactivate_admin($id)
    {
        $admin = Admin::find($id);
        
        $admin->suspended = 1;
       
        $admin->save();
        
      return back();
    }
    public function activate_admin($id)
    {
        $admin = Admin::find($id);
        
        $admin->suspended = null;
        
        $admin->save();
        
        return back();
    }
   
    private function token()
    {
        return Str::random(60);
    }
}
