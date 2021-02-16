@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9">
                
                @include('seller.about.nav')
                @foreach($our_buyers as $department =>  $buyers)
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            {{__(str_replace('_',' ',$department))    }}
                        </div>
                        <div class="card-body">
                           
                          
                                @foreach($buyers as $key=>$buyer)
                                    <a href="/seller/about/{{$buyer->buyer_company_id}}/buyer" class="list-group-item">
                                       {{$buyer->buyer_company_name}} {{__('( sales of : )')}} {{$buyer->order_value}}
                                    </a>
                                   
                                @endforeach
                                
                            
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
