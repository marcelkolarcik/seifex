@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
            @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
              
                <div class="card">
                <div class="card-header bg-secondary text-light  mb-2">
                    @include('owner.admins.nav')
                </div>
                
                <div class="card-body">
                    <h5 class="card-title">Create your admins...</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Choose wisely...</h6>
                    <form  action="{{ URL::to('/owner/create_admin') }}"  method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="container">
                            <div class="row d-flex justify-content-between align-items-center mb-2">
                               
                                    
                                    {{ Form::select('staff_role',$admin_types,null,['class' => 'mb-3 form-control form-control-sm','placeholder'=>'select admin type','required']) }}
                                    
                                
                               
                                   
                                    {{ Form::email('admin_email', null, ['class' => 'mb-3 form-control form-control-sm','placeholder'=>'Email','required']) }}
                                   
                               
                                    
                                {{ Form::text('admin_name', null ,['class' => 'mb-3 form-control form-control-sm','placeholder'=>'Name','required']) }}
                                
                                
                                
                                    <label for="submit"> &nbsp;</label>
                                    <button id="submit" class="btn btn-sm btn-primary  form-control  form-control-sm">Create New Admin</button>
                               
                            </div>
                        </div>
                    </form>
                </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
