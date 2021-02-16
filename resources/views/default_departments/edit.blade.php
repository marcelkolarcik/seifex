@extends($guard.'.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include($guard.'.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card ">
                    <div class="card-header bg-secondary text-light d-flex justify-content-between align-items-center">
                        {{__('Edit Default Department')}}
                        <div class="col-md-offset-10">
                        {!! Form::model($default_department,['method' => 'DELETE' , 'action' => [ 'DefaultDepartmentController@destroy',$default_department->id]]) !!}
                        <input class="btn btn-danger btn-sm" type="submit" value="Delete {{$default_department->department}}" >
                        {!! Form::close() !!}
                        </div>
                    </div>

                    <div class="card-body">
                        {!! Form::model($default_department,['method' => 'PATCH' , 'action' => [ 'DefaultDepartmentController@update',$default_department->id]]) !!}
                        @include('default_departments/form',['submitButtonText' => __('Save changes')] )
                        {!! Form::close() !!}



                        @include('errors.list')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
