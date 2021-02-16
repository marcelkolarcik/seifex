@extends('seller.layout.auth')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9">
                @include('includes.which_nav')
                <div class="card">
                   
                    @include('seller.staff_includes.delegations')
                
                </div>
            </div>
        </div>
    </div>
@endsection
