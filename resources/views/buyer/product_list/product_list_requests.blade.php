@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
           {{-- <div class="col-md-3">
                @include('includes.which_left_side')
            </div>--}}
            <div class="col-md-12">
               {{-- @include('includes.which_nav')--}}
                @include('buyer.product_list.requests_list')
            </div>
        </div>
    </div>

@endsection
