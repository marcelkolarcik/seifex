@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.buyer.left_side')
            </div>
            <div class="col-md-9">
                
                @include('buyer.about.nav')
                @foreach($product_lists as $department=>$product_list)
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            {{__(str_replace('_',' ',$department))}}
                        </div>
                        <div class="card-body">
    
    
                            @foreach(json_decode($product_list,true) as $product)
        
                                {{$product}}
                                @if(!$loop->last)
                                    {{' | '}}
                                @endif
                            @endforeach
                            
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
