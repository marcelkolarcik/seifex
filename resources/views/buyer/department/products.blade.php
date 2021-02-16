
@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    @include('includes.which_left_side')
                </div>
            </div>
            <div class="col-md-9">
                @include('includes.which_nav')
                <div class="card">
                    <div class="card-header">
                        <ul>
                            @if($product_list)
                                @foreach($product_list as $product)
                                    <li>{{$product}}</li>
                                @endforeach
                            @else
                                {{__('You have no products yet !')}}
                            @endif
                        </ul>
                    </div>
                    <div class="card-header bg-secondary text-light">
                        {{__('Product moves')}}
                    </div>
                    <div class="card-body">
                        
                        
                        @if($moves)
                            <table  class="table table-striped table-sm">
                                <thead>
                                <tr>
                                    <th scope="col">{{__('Moves')}}</th>
                                    <th scope="col">{{__('Product')}}</th>
                                    <th scope="col">{{__('Currently')}}</th>
                                    <th scope="col">{{__('Seller Company')}}</th>
                                   
                                </tr>
                                </thead>
                                <tbody>
                                
                               @foreach($moves as $key =>  $move_details)
                                   <tr class="{{$move_details->latest_move == 1 ? 'bg-warning':''}}">
                                   <td>
                                           <button id="{{hash('ripemd160',$move_details->product_name.$move_details->seller_company_name)}}" class="btn btn-sm btn-outline-secondary moves" >{{__('Moves :')}} {{ sizeof(json_decode($move_details->moves,true)) }}</button>
                                           <div class="product_moves d-none {{hash('ripemd160',$move_details->product_name.$move_details->seller_company_name)}}">
                                               @foreach(array_reverse(json_decode($move_details->moves,true)) as $num =>  $details)
                                                   @foreach($details as  $move => $movement_details)
                                                       {{$movements[$move]}} - {{$movement_details[0]}} - {{$movement_details[1]}} <br>
                                                   @endforeach
                                               @endforeach
                                           </div>
                                   </td>
                                   <td> {{$move_details->product_name}}</td>
                                   <td> {{$movements[$move_details->latest_move]}} </td>
                                   <td> {{ $move_details->seller_company_name }}</td>
                                   
                                   
                                   </tr>
                               @endforeach
                                
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
               
            </div>
        </div>
    </div>
@endsection



