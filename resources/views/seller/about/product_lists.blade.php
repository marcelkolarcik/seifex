@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9">
                
                @include('seller.about.nav')
                <div class="accordion" id="product_lists">
        
                    @foreach($product_lists as $department=>$product_list)
                    <div class="card">
                        <div class="card-header">
                            <button class="btn btn-link bg-secondary text-light" type="button" data-toggle="collapse" data-target="#collapse{{$department}}" aria-expanded="true" aria-controls="collapse{{$department}}">
                                {{__(str_replace('_',' ',$department))    }} {{'( '.sizeof(json_decode($product_list,true)).' )'}}
                            </button>
                        </div>
                        
                            <div id="collapse{{$department}}" class="collapse" aria-labelledby="{{$department}}" data-parent="#product_lists">
                            <div class="card-body">
                                @foreach(json_decode($product_list,true) as $product)
            
                                    {{$product}}
                                    @if(!$loop->last)
                                        {{' | '}}
                                    @endif
                                @endforeach
                            </div>
                            </div>
                        
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
