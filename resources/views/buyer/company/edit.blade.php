@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="col-md-2 ">
                
                <div class="card ">
                    <div class="card-header bg-secondary"><a class="text-light" href="{{ url('/buyer') }}">{{__('Back')}}</a></div>
                </div>
            </div>--}}
            <div class="col-md-12 ">
                <div class="card">
                   
                    @component('components.main_header')
                        {{__('Edit company account')}}
                    @endcomponent
                    
                    <div class="card-body">
                        
                        {!! Form::model($company,['method' => 'PATCH' , 'action' => [ 'Buyer\CompanyController@update',$company->id]]) !!}
                        <did class="row">
                            @include('buyer.company.form',['submitButtonText' => __('Edit company account')] )
                        </did>
                        {!! Form::close() !!}
                        
                        
                        
                       
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
