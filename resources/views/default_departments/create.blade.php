
    
        <div class="row">
            <div class="col">
                <div class="card ">
                   
                    @component('components.main_header')
                        {{__('Create Default Department')}}
                    @endcomponent
                    <div class="card-body">
                        {!! Form::model( $default_department = new \App\DefaultDepartment, ['url' => 'create_department']) !!}
                        @include('default_departments/form',['submitButtonText' => __('Create Default Department')] )
                        {!! Form::close() !!}

                        @include('errors.list')
                    </div>
                </div>
            </div>
        </div>
   


