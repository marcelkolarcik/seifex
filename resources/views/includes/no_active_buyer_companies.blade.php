
@extends('buyer.layout.auth')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
            
            </div>
            <div class="col-md-9">
                @include('includes.which_nav')
                <div class="card header bg-danger text-light justify-content-between align-items-center">
                 
                 <p class="card-title">
                     @if(\Auth::guard('buyer')->user() != null)
                         buyer
                         @endif
                         @if(\Auth::guard('seller')->user() != null)
                             seller
                         @endif
                     {{__('You have no active companies to work for at the moment !')}}
                 </p>
                </div>
            </div>
        </div>
    </div>
@endsection
