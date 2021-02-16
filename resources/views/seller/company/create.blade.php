
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
                        {{__('Create company account')}}
                    @endcomponent
                    
                    <div class="card-body">
                        
                        {!! Form::model( $SellerCompany = new \App\SellerCompany, ['url' => 'register_seller_company']) !!}
                        <div class="row">
                            @include('seller.company.form',['submitButtonText' => __('Create Company')] )
                        </div>
                        {!! Form::close() !!}
                        
                       
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
