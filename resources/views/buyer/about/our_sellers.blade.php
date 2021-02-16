@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.buyer.left_side')
            </div>
            <div class="col-md-9">
                
                @include('buyer.about.nav')
                @foreach($our_sellers as $department =>  $sellers)
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            {{__(str_replace('_',' ',$department))}}
                        </div>
                        <div class="card-body">
                           
                          
                                @foreach($sellers as $key=>$seller)
                                    <a href="/buyer/about/{{$seller->seller_company_id}}/seller" class="list-group-item">
                                       {{$seller->seller_company_name}} {{__(' ( purchases of : ) ')}} {{$seller->order_value}}
                                    </a>
                                   
                                @endforeach
                                
                            
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
