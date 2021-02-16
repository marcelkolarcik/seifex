@extends($guard.'.layout.auth')

        @section('content')
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        @include($guard.'.includes.left_side')
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col">
                                <div class="card ">
                                   
                                    @component('components.main_header')
                                        {{__('Create Default Duty')}}
                                    @endcomponent
                                    <div class="card-body">
                                        {!! Form::model( $duty = new \App\Duty, ['url' => 'create_duty']) !!}
                                        @include('staff_duties.form',['submitButtonText' => __('Create Default Duty')] )
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

   


