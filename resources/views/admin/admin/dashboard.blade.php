@extends('admin.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('admin.includes.left_side')
            </div>
            <div class="col-md-9">
    
                <div class="card-header bg-secondary text-light">{{__('Admin dashboard')}}</div>
                <div class="card-body">
               
                </div>
            
            </div>
            
         </div>
    </div>
@endsection
