
@extends($guard.'.layout.auth')
@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="col-md-3 ">--}}
               {{----}}
                {{--@include('includes.which_left_side')--}}
               {{----}}
            {{--</div>--}}
            <div class="col-md-12 printable"  id="printable">
                <button class="print d-print-none form-control btn-secondary text-light"> {{__('Print this invoice')}}</button>
                <div class="card">
                    <div class="card-header bg-transparent d-flex justify-content-between ">
                        <div class="invoice">
                      <h3>{{__('period')}} :  {{ __(str_replace('i','y',str_replace('ly','',explode('_',$invoice_period[$invoice->invoice_freq])[0])))  }} </h3>
                          <b>  {{ __(str_replace('i','y',str_replace('ly','',explode('_',$invoice_period[$invoice->invoice_freq])[0])))  }} : {{ __($invoice->period) }}</b>
                            <br>
                            {{__('Invoice')}} {{__('date')}} :   {{$invoice->created_at}}
                            <br>
                            {{__('Invoice')}} {{__('number')}} :   {{$invoice->id}}
                            <br>
                         {{__('Account')}} {{__('number')}} (to be adjusted):   {{$invoice->buyer_company_id}}
                         
                        </div>
                            <div class="seller">
                           
                             <h3>   {{$invoice->seller_company_name}} </h3>
                                <?php $a=1 ?>
                              @foreach(json_decode($invoice->seller_company_address,true) as $line)
                                   {{$line}}
                                @if( $a%2 != 0  )
                                    |
                                @else
                                        <br>
                                @endif
                                  <?php $a++ ?>
                              @endforeach
                                <?php $a=1 ?>
                           {{__('Tel')}} : {{$invoice->seller_phone_number}}
                                <br>
                                
                           {{__('Email')}} : {{$invoice->seller_email}}
                           
                        </div>
                       
                    </div>
                   
                    <div class="card-header bg-transparent d-flex justify-content-between">
                      
                            <div class="buyer">
                                {{__('Invoice')}} {{__('for')}} :<br>
                               <h3> {{$invoice->buyer_company_name}} </h3>
                                @foreach(json_decode($invoice->buyer_company_address,true) as $line)
                                   {{$line}}
                                    @if( $a%2 != 0  )
                                        |
                                    @else
                                        <br>
                                    @endif
                                    <?php $a++ ?>
                                @endforeach
                               
                            </div>
                            <div class="confirmation">
                                @if($invoice->confirmed_at)
                                    <button class="btn btn-sm btn-success disabled">
                                        {{__('CONFIRMED')}} &#10004;
                                    </button>
                                @elseif($invoice->paid_at)
                                    <button class="btn btn-sm btn-outline-success disabled">
                                        {{__('MARKED AS PAID')}} &#10004;
                                    </button>
                                    @if (Auth::guard('seller')->user() && Auth::guard('seller')->user()->can('confirm_invoice_as_paid', App\Invoice::class))
                                        <button class="btn btn-sm btn-danger confirm_invoice"
                                                id="{{$invoice->id}}"
                                                title               =     "{{__('Confirm invoice as paid ?')}}"
                                                wrong               = "{{__('Something went wrong.')}}"
                                                later               = "{{__('Please try again later.')}}"
                                        >
                                            
                                            {{__('CONFIRM')}}
                                            
                                        </button>
                                     @endif
                                @elseif($invoice->paid_at == null && \Auth::guard('buyer')->user() )
                                   
                                    @if( Auth::guard('buyer')->user()->can('mark_invoice_as_paid', App\Invoice::class))
                                    <button class="btn btn-sm btn-primary mark_as_paid_invoice"
                                            id="{{$invoice->id}}"
                                            buyer_company_id    =   "{{$invoice->buyer_company_id}}"
                                            seller_company_id   =   "{{$invoice->seller_company_id}}"
                                            period              =   "{{$invoice->period}}"
                                            title               =     "{{__('Mark as paid ?')}}"
                                            wrong               =       "{{__('Something went wrong.')}}"
                                            later               =       "{{__('Please try again later.')}}"
                                    >
                                       {{__('mark as paid')}}
                                    </button>
                                    @endif
                                @endif
                                    <hr> <b>{{__('Total')}} : {{$invoice->invoice_cost}} EUR</b>
                            </div>
                           
                       
                    </div>
                    <div class="card-body ">
                        <table class="table table-sm ">
                            <tr>
                                <td>{{__('Order')}} {{__('number')}}</td>
                                <td>{{__('Department')}}</td>
                                <td>{{__('Date')}}</td>
                                <td>{{__('Price')}}</td>
                                <td class="d-print-none">{{__('Ordering')}}</td>
                            </tr>
                       
                    @foreach($orders as $order)
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>{{ __(str_replace('_',' ',$order->department)) }}</td>
                                <td>{{$order->created_at}}</td>
                                <td>{{$order->total_order_cost}} EUR</td>
                                <td  class="d-print-none"> <a href="/order/{{$order->id}}/{{$order->$company_id}}">{{__('check order')}}</a></td>
                            </tr>
                       {{-- <div class="d-flex justify-content-between align-items-center">
                            <a href="/order/{{$order->id}}/{{$order->$company_id}}">{{str_replace('_',' ',$order->department)}} {{$order->created_at}}</a>
                            <p>{{$order->total_order_cost}} EUR</p>
                        </div>--}}
                    @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>{{__('Total')}} : {{$invoice->invoice_cost}} EUR</b></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                       <small> {{$invoice->seller_company_name}} | {{__('VAT Registration Number')}} : {{$invoice->seller_VAT}}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
