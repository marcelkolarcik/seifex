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
                    @include('owner.roles.nav')
                </div>
                
                <div class="card-body">
                    <h5 class="card-title">Create role.</h5>
                    {!!  Form::open(['method' => 'POST','action' => [ 'Owner\RoleController@create']])!!}
                   
                        @csrf
                        <div class="container">
                            <div class="row d-flex justify-content-between align-items-center mb-2">
                                 {{ Form::select('guard',$guards,null,['class' => 'mb-3 form-control form-control-sm','placeholder'=>'role for...','required']) }}
                                  {{ Form::text('name', null ,['class' => 'mb-3 form-control form-control-sm','placeholder'=>'Role name','required']) }}
                                    <label for="submit"> &nbsp;</label>
                                    <button id="submit" class="btn btn-sm btn-primary  form-control  form-control-sm">Create new role</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
