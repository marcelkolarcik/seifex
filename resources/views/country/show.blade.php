<?php
if(\Auth::guard('owner')->user()) $guard='owner';
if(\Auth::guard('admin')->user()) $guard='admin';
?>

@extends($guard.'.layout.auth')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @if(\Auth::guard('owner')->user())
                    @include('owner.includes.left_side')
                @elseif(\Auth::guard('admin')->user())
                    @include('admin.includes.left_side')
                @endif
               
            </div>
            <div class="col-md-9">
                <div class="card">
                
                    <div class="card-header bg-secondary text-light">
                        {{$country->country_name}} {{$sales}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @foreach($buyers as $buyer)
                                    {{$buyer->buyer_company_name}}
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                @foreach($sellers as $seller)
                                    {{$seller->seller_company_name}}
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
