@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9">
                
                @include('seller.about.nav')
                @foreach($prices as $department=>$price_list)
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            {{__(str_replace('_',' ',$department))    }}
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-sm">
    
                                <thead>
                                <tr>
                                    <th scope="col">{{__('Name')}}</th>
                                    <th scope="col">{{__('per kg')}}</th>
                                    <th scope="col">{{__('per box')}}</th>
                                    <th scope="col">{{__('type / brand')}}</th>
                                    <th scope="col">{{__('Additional info')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                           
                                @foreach(json_decode($price_list,true) as $product=>$data)
                                    <tr >
                                        <td>
                                            {{explode('+',$product)[0]}}
                                        </td>
                                        <td>
                                            {{$data['price_per_kg']}}
                                        </td>
                                        <td>
                                            {{$data['box_price']}}
                                        </td>
                                        <td>
                                            {{$data['type_brand']}}
                                        </td>
                                        <td>
                                            {{$data['additional_info']}}
                                        </td>
                                       
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
