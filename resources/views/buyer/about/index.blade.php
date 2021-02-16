@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.buyer.left_side')
            </div>
            <div class="col-md-9">
                
                @include('buyer.about.nav')
                @foreach($companies as $company)
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="card">
                                        <div class="card-header bg-primary text-light">
                                            <h3>   {{$company[0]->buyer_company_name}} </h3> <span>{{__('Joined Seifex : ')}} </span>
                                            <span> {{ Carbon\Carbon::parse($company[0]->created_at)->diffForHumans() }}</span>
                                        </div>
                                        <div class="card-body">
                                            @foreach(json_decode($company[0]->address,true) as $line)
                                                {{$line}}
                                                @if(!$loop->last)
                                                    {{' | '}}
                                                @endif
                                            @endforeach
                                            <hr>
                                            @foreach($location[$company[0]->id] as $loc)
                                                {{str_replace('-',' ',$loc)}}
                                                @if(!$loop->last)
                                                    {{' | '}}
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-primary text-light">
                                            <h3>  {{__('Sales')}} </h3>
                                        </div>
                                        <div class="card-body">
                                            @foreach($all_sales as $id  =>  $departments)
                                                <h5> {{$company[0]->buyer_company_name }}</h5>
                                                @foreach($departments as $department  =>  $sale)
                                                     {{__(str_replace('_',' ',$department))}}   {{' : '}} {{$sale}}
                                                    @if(!$loop->last)
                                                        <hr>
                                                    @endif
                                                
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            {{__('Owner')}}
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">{{$company[0]->buyer_owner_name}}</li>
                                            <li class="list-group-item">{{$company[0]->buyer_owner_email}}</li>
                                            <li class="list-group-item">{{$company[0]->buyer_owner_phone_number}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            {{__('Buyer')}}
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">{{$company[0]->buyer_name}}</li>
                                            <li class="list-group-item">{{$company[0]->buyer_email}}</li>
                                            <li class="list-group-item">{{$company[0]->buyer_phone_number}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            {{__('Acountant')}}
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">{{$company[0]->buyer_accountant_name}}</li>
                                            <li class="list-group-item">{{$company[0]->buyer_accountant_email}}</li>
                                            <li class="list-group-item">{{$company[0]->buyer_accountant_phone_number}}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
