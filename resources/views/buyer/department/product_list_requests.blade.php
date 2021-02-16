@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    @include('includes.buyer.left_side')
                </div>
            </div>
            <div class="col-md-9">
                @include('buyer.department.nav')
                @include('buyer.product_list.requests_list')
            </div>
        </div>
    </div>
@endsection



