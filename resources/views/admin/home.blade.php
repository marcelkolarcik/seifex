@extends('admin.layout.auth')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-3">
            @include('admin.includes.left_side')
        </div>
        <div class="col-md-9">
           
            <div class="card">
                <div class="card-header">{{__('Dashboard')}}</div>
                <div class="card-body">
                    {{__('You are logged in as Admin!')}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
