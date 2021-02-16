@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.buyer.left_side')
            </div>
            <div class="col-md-9">
              {{--  @include('buyer.owner.nav')--}}
            </div>
            
        </div>
     </div>
   
@endsection
