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
                       @include('owner.statistics.nav')
                    </div>
                    <div class="card-body">
                        @include('includes.company_statistics')
                    </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
