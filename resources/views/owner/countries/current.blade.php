@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card">
    
                    @include('owner.includes.current_countries')
                
                </div>
            </div>
        </div>
    </div>

@endsection
