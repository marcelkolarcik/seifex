@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           {{-- <div class="col-md-3">
                    @include('includes.buyer.left_side')
            </div>--}}
            <div class="col-md-12">
    
                   {{-- @include('buyer.department.nav')--}}
                    @include('includes.orders_list')
            </div>
        </div>
    </div>
@endsection



