@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9">
                
                @include('seller.about.nav')
                @foreach($delivery_locations as $department=>$locations)
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            {{__(str_replace('_',' ',$department))    }}
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($locations as $key=>$location)
                                    <li class="list-group-item">
                                        {{$location['country']}}
                                        {{ !empty($location['county']) ? ' -> ' : ''}}
                                        {{ !empty($location['county']) ? $location['county'] : ''}}
                                        {{ !empty($location['county_l4']) ? ' -> ' : ''}}
                                        {{ !empty($location['county_l4']) ? $location['county_l4'] : ''}}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
