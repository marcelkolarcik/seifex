@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card ">
                    <div class="card-header bg-secondary text-light d-flex justify-content-between align-items-center">
                        Edit Admin Duty
                        <div class="col-md-offset-10">
                        {!! Form::model($duty,['method' => 'DELETE' , 'action' => [ 'Owner\AdminDutiesController@destroy',$duty->id]]) !!}
                        <input class="btn btn-danger btn-sm" type="submit" value="Delete {{$duty->duty_name}}" >
                        {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="card-body">
                        {!! Form::model($duty,['method' => 'PATCH' , 'action' => [ 'Owner\AdminDutiesController@update',$duty->id]]) !!}
                        @include('owner.admins.create_admin_duty_form',['submitButtonText' => 'Save changes'] )
                        {!! Form::close() !!}



                        @include('errors.list')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
