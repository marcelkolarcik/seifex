@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
               @include('includes.which_left_side')
            </div>
            <div class="col-md-9">
                @include('includes.which_nav')
                @include('seller.product_list.requests_list')
            </div>
        </div>
    </div>
@endsection



