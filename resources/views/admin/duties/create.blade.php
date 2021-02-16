@extends('admin.layout.auth')

        @section('content')
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        @include('admin.includes.left_side')
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col">
                                <div class="card ">
                                    <div class="card-header bg-secondary text-light">{{__('Create Default Duty')}}</div>
                                    <div class="card-body">
                                        {!! Form::model( $duty = new \App\Duty, ['url' => 'create_duty']) !!}
                                        @include('admin.duties.form',['submitButtonText' => __('Create Default Duty')] )
                                        {!! Form::close() !!}
                    
                                        @include('errors.list')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

   


