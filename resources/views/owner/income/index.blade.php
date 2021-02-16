@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-secondary text-light">
                        @include('owner.income.nav')
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            Turnover : {{$income/0.035}} EUR
                        </h5>
                        <hr>
                        <h5 class="card-title">
                            Income : {{$income}} EUR
                        </h5>
                        
                    </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
