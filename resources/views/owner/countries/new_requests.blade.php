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
                        @include('owner.countries.nav')
                    </div>
                    <ul class="list-group">
                    @foreach($countries as $country=>$country_a)
                            <li class="list-group-item">
                               {{$country}} : {{sizeof($country_a)}}
                            </li>
                    @endforeach
                    </ul>
                    </div>
                
                </div>
            </div>
        </div>
   

@endsection
