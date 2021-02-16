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
                        @if(isset($countries))
                            <ul class="list-group">
                                @foreach($countries as $country=>$data)
                                    <li class="list-group-item">
                                        <a href="{{$type}}/{{$data['country_id']}}"> {{$country}} => {{$data['order_value']}} EUR</a>
                                       
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if(isset($turnover))
                            {{$turnover}}
                        @endif
                    </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
