@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="col-md-2 ">--}}
                {{--<div class="card ">--}}
                    {{--<div class="card-header bg-secondary"><a class="text-light" href="{{ url('/buyer') }}">{{__('Back')}}</a></div>--}}
                {{--</div>--}}
    {{----}}
            {{--</div>--}}
            <div class="col-md-12 ">
                <div class="card ">
                    @component('components.main_header')
                        {{__('Create company account')}}
                    @endcomponent
                    @if($first)
                        {{__('This is your first company!')}}
                    @endif
                    <div class="card-body">
                        {!! Form::model( $company = new \App\BuyerCompany, ['url' => 'register_buyer_company']) !!}
                        <div class="row">
                           
                            @include('buyer.company.form',['submitButtonText' => __('Create Company Account')] )
                           
                        </div>
                        {!! Form::close() !!}
                       
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
