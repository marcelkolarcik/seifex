@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9">
                @include('seller.owner.nav')
            </div>
        </div>
    </div>

@endsection
