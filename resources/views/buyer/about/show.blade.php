@extends('front.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
               {{--@include('includes.buyer.left_side')--}}
            </div>
            <div class="col-md-9">
                
               {{-- @include('buyer.about.nav')--}}
               
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            {{$company->buyer_company_name}}
                        </div>
                        <div class="card-body">
                        
                        </div>
                    </div>
               
            </div>
        </div>
    </div>
@endsection
