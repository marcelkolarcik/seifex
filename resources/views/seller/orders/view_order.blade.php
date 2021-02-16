@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           
            <div class="col-md-12 printable"  id="printable">
                <button class="print d-print-none form-control btn-secondary text-light" id="printer"> {{__('Print this pick list')}}</button>
                <div class="card ">
                    <div class="card-header d-print-none">
                        @include('includes.order_payment_conf')
                    </div>
                    
                    <div class="card-body">
                        <table class="table table-condensed table-bordered">
                        
                            <tr>
                                <td>{{__('Company :')}} {{$order->buyer_company_name}} </td>
                                <td>{{$order->buyer_company_name}} {{__('Id :')}} {{$order->id}}</td>
                                <td>{{$order->buyer_name}} {{$order->buyer_phone_number}}</td>
                            </tr>
                            <tr>
                                <td>{{__('Date of order :')}}  </td>
                                <td> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->toDayDateTimeString() }}</td>
                                <td>{{__('Order Id :')}} {{$order->id}}</td>
                            </tr>
                            <tr>
                                <td>{{__('Date of delivery :')}}  </td>
                                <td> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->addDay()->format('D M j, Y ').'morning' }}</td>
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
                                        <td>{{__('kg')}}</td>
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
                                <td style="border-bottom: groove; border-top: groove">{{number_format((float)$order->total_order_cost, 2, '.', '')}}
                                    {{$order->currency}}</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer d-print-none">
                        
                        @if($order->prepped_at == null && $order->delivered_at == null )
                            @if (Auth::guard('seller')->user()->can('seller_interact_with_orders', App\Order::class))
                            <form  action="{{ URL::to('order_dispatched') }}"  method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <button class="btn btn-outline-success btn-sm hidden-print order_dispatched prepped"
                                         id                         =   "{{$order->id}}"
                                         data-buyer_company_id      =   "{{$order->buyer_company_id}}"
                                         data-buyer_company_name    =   "{{$order->buyer_company_name}}"
                                         data-buyer_email           =   "{{$order->buyer_email}}"
                                         data-seller_company_id     =   "{{$order->seller_company_id}}"
                                         data-seller_company_name   =   "{{$order->seller_company_name}}"
                                         data-seller_email          =   "{{$order->seller_email}}"
                                         data-department            =   "{{$order->department}}"
                                         data-wrong                 =   "{{__('Something went wrong.')}}"
                                         data-later                 =   "{{__('Please try again later.')}}"
                                >
                                    {{__('Change status to dispatched')}}
                                </button>
                            </form>
                            @endif
                        @elseif($order->prepped_at != null && $order->delivered_at == null)
                                @if (Auth::guard('seller')->user()->can('seller_interact_with_orders', App\Order::class))
                            <form  action="{{ URL::to('order_delivered') }}"  method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <button class="btn btn-outline-success btn-sm hidden-print order_delivered delivered hidden"
                                        id                         =   "{{$order->id}}"
                                        data-buyer_company_id      =   "{{$order->buyer_company_id}}"
                                        data-buyer_company_name    =   "{{$order->buyer_company_name}}"
                                        data-buyer_email           =   "{{$order->buyer_email}}"
                                        data-seller_company_id     =   "{{$order->seller_company_id}}"
                                        data-seller_company_name   =   "{{$order->seller_company_name}}"
                                        data-seller_email          =   "{{$order->seller_email}}"
                                        data-department            =   "{{$order->department}}"
                                        data-wrong                 =   "{{__('Something went wrong.')}}"
                                        data-later                 =   "{{__('Please try again later.')}}"
                                >
                                    {{__('Change status to delivered')}}
                                </button>
                            </form>
                            @endif
                        @elseif($order->prepped_at != null && $order->delivered_at != null)
                          
                                <button disabled="disabled" class="btn btn-outline-success btn-sm hidden-print order_delivered delivered"  delivered="" id={{$order->id}}>
                                    {{__('Delivered')}}</button>
                          
                        
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
