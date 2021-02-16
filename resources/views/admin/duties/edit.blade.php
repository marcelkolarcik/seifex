@extends('admin.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('admin.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card ">
                    <div class="card-header bg-secondary text-light d-flex justify-content-between align-items-center">
                        {{__('Edit Default Duty')}}
                        <div class="col-md-offset-10">
                        {!! Form::model($duty,['method' => 'DELETE' , 'action' => [ 'Admin\DutyController@destroy',$duty->id]]) !!}
                        <input class="btn btn-danger btn-sm" type="submit" value="Delete {{$duty->duty_name}}" >
                        {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="card-body">
                        {!! Form::model($duty,['method' => 'PATCH' , 'action' => [ 'Admin\DutyController@update',$duty->id]]) !!}
                        @include('admin.duties.form',['submitButtonText' => __('Save changes')] )
                        {!! Form::close() !!}



                        @include('errors.list')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
