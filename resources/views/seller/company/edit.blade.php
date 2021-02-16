
@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           {{-- <div class="col-md-2 ">
                <div class="card ">
                    <div class="card-header bg-secondary"><a class="text-light" href="{{ url('/seller') }}">{{__('Back')}}</a></div>
                </div>
            
            </div>--}}
            <div class="col-md-12 ">
                <div class="card">
                    @component('components.main_header')
                        {{__('Edit company account')}} <a class="btn btn-sm  text-light_green"
                                                          href="/requests">
                            {{__('Cooperation requests')}}
        
                        </a><a class="btn btn-sm  text-light_green"
                               href="/requests">
                            {{__('Cooperation requests')}}
        
                        </a><a class="btn btn-sm  text-light_green"
                               href="/requests">
                            {{__('Cooperation requests')}}
        
                        </a>
                    @endcomponent
                    <div class="card-body">
                        
                        {!! Form::model($company,['method' => 'PATCH' , 'action' => [ 'Seller\CompanyController@update',$company->id]]) !!}
                        <div class="row">
                            @include('seller.company.form',['submitButtonText' => __('Edit company account')] )
                        </div>
                        {!! Form::close() !!}
                      
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
