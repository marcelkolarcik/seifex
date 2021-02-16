@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="col-md-3 ">--}}
              {{--@include('includes.which_left_side')--}}
              {{--<a class="list-group-item" href="{{ url('/orders') }}">{{__('Orders')}}</a>--}}
                {{----}}
            {{--</div>--}}
            <div class="col-md-12 printable"  id="printable">
                <button class="print d-print-none form-control btn-secondary text-light"> {{__('Print this pick list')}}</button>
                <div class="card ">
                    <div class="card-header">
                      {{--  @include('includes.order_payment_conf')--}}
                        @if( $order->delivered_at != null)
                            <br> <span class="text-monospace text-primary"> {{__('Delivered')}}
                                {{\Carbon\Carbon::parse($order->delivered_at)->diffForHumans()}} &#10004;</span>
                            @if($order->buyer_confirmed_delivery_at == null)
                                @if (Auth::guard('buyer')->user()->can('buyer_interact_with_orders', App\Order::class))
                                <small class="text-danger">{{__('Please confirm !')}}</small>
                                @endif
                            @endif
                        @elseif( $order->prepped_at != null)
                            <br> <span class="text-monospace text-primary"> {{__('Dispatched')}}
                                {{\Carbon\Carbon::parse($order->prepped_at)->diffForHumans()}} &#10004;</span>
                        @endif
                        <div class="d-print-none">
                        @if($order->buyer_confirmed_delivery_at != null)
                            <span class="text-monospace text-primary"> {{__('Delivery confirmed')}}  &#10004;</span>
                            @if($order->comment != null)
                                <hr>
                                <span class="text-monospace text-primary">   {{__('Comment :')}} </span>
                                <span >
                                    {{$order->comment}}
                                </span>
                            @endif
                        @elseif( $order->delivered_at != null)
                            @if (Auth::guard('buyer')->user()->can('buyer_interact_with_orders', App\Order::class))
                            <form  action="{{ URL::to('buyer_confirmed_delivery') }}"  method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <label  class="small" for="order_comment">{{__('If you have any comment, please share :')}}</label>
                                {{ Form::textarea('order_comment', null,
                                ['class' => 'form-control form-control-sm','id' =>  'order_comment','placeholder'=> __('optional')   ,'rows'=>1]) }}
                                <hr>
                                <button  class="btn btn-outline-success btn-sm hidden-print order_delivery_confirmed"
                                         id                         =   "{{$order->id}}"
                                         data-buyer_company_id      =   "{{$order->buyer_company_id}}"
                                         data-buyer_company_name    =   "{{$order->buyer_company_name}}"
                                         data-seller_company_id     =   "{{$order->seller_company_id}}"
                                         data-seller_company_name   =   "{{$order->seller_company_name}}"
                                         data-seller_email          =   "{{$order->seller_email}}"
                                         data-department            =   "{{$order->department}}"
                                         data-wrong                 =   "{{__('Something went wrong.')}}"
                                         data-later                 =   "{{__('Please try again later.')}}"
                                >
                
                                    {{__('Confirm delivery')}}
                                </button>
                            </form>
                            @endif
                        @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-condensed table-bordered">
                           
                            <tr>
                                <td>{{__('Company : ')}}  {{$order->seller_company_name}}</td>
                                <td> {{__('Order Id :')}} {{$order->id}}</td>
                                <td>{{$order->seller_name}} {{$order->seller_phone_number}}</td>
                            </tr>
                            <tr>
                                <td>{{__('Date of order :')}}  </td>
                                <td> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->toDayDateTimeString() }}</td>
                                <td>{{__('Ordering')}} {{$order->id}}</td>
                            </tr>
                            <tr>
                                <td>{{__('Date of delivery :')}}  </td>
                                <td> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->addDay()->format('D M j, Y ') }}</td>
                                <td>{{__('Checked By :')}}</td>
                            </tr>
                          
                            {{--@if($order->extra != '')--}}
                            {{--<tr>--}}
                                {{--<td class="bg-secondary text-light" colspan="3">--}}
                                    {{--{{__('Extra :')}} {{ $order->extra }}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            {{--@endif--}}
                           
                            @if(json_decode($order->not_available) !== [])
                                <tr>
                                    <td class="bg-danger text-light" colspan="3">
                                        {{__('Not available products :')}}
                                        @foreach(json_decode($order->not_available) as $not_available_product)
                                            {{explode('+',$not_available_product)[0]}},
                                        @endforeach
                                      
                                    </td>
                                </tr>
                            @endif
                        </table>
                        <table class="table table-condensed ">
                            <tr style="border-bottom: groove; border-top: groove">
                                <td>{{__('Qty')}}</td>
                                <td>{{__('Unit')}}</td>
                                <td>{{__('Product')}}</td>
                                <td>{{__('Code')}}</td>
                                <td>{{__('Unit price')}}</td>

                                <td>{{__('VAT')}}</td>
                                <td>{{__('TUP ex VAT')}}</td>
                                <td>{{__('Sub Total')}}</td>
                                <td>{{__('Delivered')}}</td>
                            </tr>
                           @foreach(json_decode($order->order,true) as $product=>$data)
                                <tr>
                               @if(isset($data['box_size']))
                                        <td>{{$data['amount']}}</td>
                                        <td>{{__('box')}} <small>{{$data['box_size']}} {{__('kg')}}</small></td>
                                        <td >{{$product}}<br><small class="text-primary"  >{{$data['type_brand']}}</small></td>
                                        <td>{{$data['product_code']}}</td>
                                        <td>{{$data['total_product_price_box']/$data['amount']}}</td>

                                        <td>{{$data['total_product_price_box'] *0.12}}</td>
                                        <td>{{$data['total_product_price_box'] - $data['total_product_price_box'] *0.12}}</td>
                                        <td>{{$data['total_product_price_box']}}</td>
                                        <td style="border-left: double"></td>
                               @else
                                        <td>{{$data['amount']}}</td>
                                        <td>kg</td>
                                        <td>{{$product}}<br><small class="text-primary"  >{{$data['type_brand']}}</small></td>
                                        <td>{{$data['product_code']}}</td>
                                        <td>{{$data['total_product_price']/$data['amount']}}</td>

                                        <td>{{$data['total_product_price'] *0.12}}</td>
                                        <td>{{$data['total_product_price'] - $data['total_product_price'] *0.12}}</td>
                                        <td>{{$data['total_product_price']}}</td>
                                        <td style="border-left: double"></td>
                               @endif
                                </tr>
                           @endforeach
                          
                            <tr >
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="border-bottom: groove; border-top: groove">{{__('Total')}}</td>
                                <td style="border-bottom: groove; border-top: groove">{{number_format((float)$order->total_order_cost, 2, '.', '')}} {{$order->currency}}</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>

@endsection
