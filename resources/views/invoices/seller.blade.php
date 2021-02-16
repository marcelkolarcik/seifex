@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            
            <div class="col-md-12">
                {{-- @include('includes.which_nav')--}}
                {{--  {{dd($company_totals)}}--}}
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            @if(!isset(explode('/',$_SERVER['REQUEST_URI'])[2]) )
                                <li class="nav-item">
                                    <a class="nav-link active" href="/invoices">{{__('All')}} {{__('Invoices')}}</a>
                                </li>
                            @else
                                <a class="nav-link " href="/invoices">{{__('All')}} {{__('Invoices')}}</a>
                            @endif
        
                            @if( isset($invoices['daily_invoices']) &&  $invoices['daily_invoices'] != [] )
           
                                    <li class="nav-item">
                                        <a class="nav-link {{$daily_invoices}}" href="/invoices/1">{{__('Daily')}} {{__('Invoices')}} ({{sizeof($invoices['daily_invoices'])}})</a>
                                    </li>
        
                            @endif
                            @if(isset($invoices['weekly_invoices'])  &&  $invoices['weekly_invoices'] != [])
        
                                    <li class="nav-item">
                                        <a class="nav-link {{$weekly_invoices}}" href="/invoices/2">{{__('Weekly')}} {{__('Invoices')}} ({{sizeof($invoices['weekly_invoices'])}})</a>
                                    </li>
                            @endif
                            @if(isset($invoices['monthly_invoices']) &&  $invoices['monthly_invoices'] != [])
        
                                    <li class="nav-item">
                                        <a class="nav-link {{$monthly_invoices}}" href="/invoices/3">{{__('Monthly')}} {{__('Invoices')}} ({{sizeof($invoices['monthly_invoices'])}})</a>
                                    </li>
                            @endif
    
    
                        </ul>

                    </div>
    
                    @if( isset(explode('/',$_SERVER['REQUEST_URI'])[2]) && in_array(explode('/',$_SERVER['REQUEST_URI'])[2],[1,2,3])  )
                        <div class="d-flex justify-content-between align-items-center ">
                            <a class="nav-link {{$unpaid_invoices}}" href="/invoices/{{explode('/',$_SERVER['REQUEST_URI'])[2]}}/1">{{__('Not paid')}} </a>
                            <a class="nav-link {{$marked_as_paid_invoices}}" href="/invoices/{{explode('/',$_SERVER['REQUEST_URI'])[2]}}/2">{{__('Marked as paid')}}</a>
                            <a class="nav-link {{$confirmed_as_paid_invoices}}" href="/invoices/{{explode('/',$_SERVER['REQUEST_URI'])[2]}}/3">{{__('Confirmed as paid')}}</a>
                        </div>
                    @endif
                   
                    <ul class="list-group">
                        @foreach($invoices as $invoice_period   =>  $periods)
                            @if($periods)
                                <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-light mt-2" >
                                    <h4>{{__(str_replace('_',' ',$invoice_period))}}</h4>
                                    <h4>
                                        
                                      
                                        {{ __('current '.str_replace('i','y',str_replace('ly','',explode('_',$invoice_period)[0]))) .' :'}}
                                        
                                        @if($invoice_period ==  'daily_invoices')
                                            {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::today())->toDateString() }}
                                        @elseif($invoice_period ==  'weekly_invoices')
                                            {{ \Carbon\Carbon::now()->weekOfYear }}
                                        @elseif($invoice_period ==  'monthly_invoices')
                                            {{ \Carbon\Carbon::now()->shortEnglishMonth }}
                                        @endif
                                        @if($invoice_period !=  'daily_invoices')
                                        - {{ \Carbon\Carbon::now()->year}}</h4>
                                        @endif
                                
                                </div>
                            @endif
                            @foreach($periods as $date  =>  $companies)
                                <div class="img-thumbnail mb-3">
                                
                               
                                {{--<div class="card-header bg-transparent text-primary text-monospace ">
                                    <b>
                                    {{  str_replace('i','y',str_replace('ly','',explode('_',$invoice_period)[0]))  }}  :  {{$date}} - total for the  {{  str_replace('i','y',str_replace('ly','',explode('_',$invoice_period)[0]))  }} -  {{$totals[$invoice_period][$date]}} EUR
                                    </b>
                                </div>--}}
                            <div class="accordion" id="invoices">
                                <b>
                                    {{ __(str_replace('i','y',str_replace('ly','',explode('_',$invoice_period)[0]))) }}  :
                                    {{$date}} - {{__('total for the')}}
                                    {{ __(str_replace('i','y',str_replace('ly','',explode('_',$invoice_period)[0]))) }} -
                                    
                                    {{--@foreach($totals[$invoice_period][$date] as $currency => $amount)--}}
                                        {{--{{$amount}} &nbsp; {{$currency}}--}}
                                    {{--@endforeach--}}
                                  
                                    @foreach($totals[$invoice_period][$date] as $currency => $amount)
                                    
                                        {{($amount)}} {{$currency}}
                                    @endforeach
                                </b>
                                @foreach($companies as $company =>  $orders)
                                    
                                    <li class="list-group-item text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            
                                            <button class="btn btn-link text-dark" type="button" data-toggle="collapse"
                                                    data-target="#collapse{{ hash('adler32',array_values( $company_totals[$invoice_period][$date][$company])[0])}}"
                                                    aria-expanded="true"
                                                    aria-controls="collapse{{ hash('adler32',array_values( $company_totals[$invoice_period][$date][$company])[0])}}">
                                                
                                                {{$company}} - {{__('total')}}
                                                
                                                @foreach($company_totals[$invoice_period][$date][$company] as $currency => $amount)
                                                 
                                                    {{$amount}} &nbsp; {{$currency}}
                                                @endforeach
                                                
                                            </button>
                                            
                                            @if(isset($paid_invoices[$date][$company]))
                                                <a href="/invoice/{{$paid_invoices[$date][$company][0]->id}}/{{$paid_invoices[$date][$company][0]->seller_company_id}}"
        
        
                                                   class="btn btn-sm btn-outline-primary preview_invoice" id="{{$paid_invoices[$date][$company][0]->id}}">
                                                    {{__('preview invoice id :')}} {{$paid_invoices[$date][$company][0]->id}}
                                                </a>
                                                @if($paid_invoices[$date][$company][0]->paid_at)
                                                <button class="btn btn-sm btn-outline-success disabled">
                                                    {{__('MARKED AS PAID')}} &#10004;
                                                </button>
                                                    @if($paid_invoices[$date][$company][0]->confirmed_at)
                                                        <button class="btn btn-sm btn-success disabled">
                                                            {{__('CONFIRMED')}} &#10004;
                                                        </button>
                                                    @else
                                                        @if (Auth::guard('seller')->user()->can('confirm_invoice_as_paid', App\Invoice::class))
                                                        <button class="btn btn-sm btn-danger confirm_invoice"
                                                                id="{{$paid_invoices[$date][$company][0]->id}}"
                                                                title               =     "{{__('Confirm invoice as paid ?')}}"
                                                                wrong               = "{{__('Something went wrong.')}}"
                                                                later               = "{{__('Please try again later.')}}">
                                                            
                                                            {{__('CONFIRM')}}
                                                        </button>
                                                        @endif
                                                    @endif
                                                @else
                                                    <button class="btn btn-sm btn-primary disabled">
                                                        {{__('request sent to buyer')}} &#10004;
                                                    </button>
                                                @endif
                                            @else
                                                @if($invoice_period ==  'daily_invoices')
                                                    @if(\Carbon\Carbon::createFromFormat('D-d-M-Y', $date)->toDateString() !=
                                                    \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::today())->toDateString())
                                                        @if (Auth::guard('seller')->user()->can('send_invoice', App\Invoice::class))
                                                        <button class="btn btn-sm btn-primary send_invoice"
                                                                
                                                                buyer_company_id    =   "{{$orders[0]->buyer_company_id}}"
                                                                seller_company_id   =   "{{$orders[0]->seller_company_id}}"
                                                                period              =   "{{$date}}"
                                                                invoice_freq        =   "{{$orders[0]->invoice_freq}}"
                                                                order_ids           =   "{{json_encode($order_ids[$invoice_period][$date][$company])}}"
                                                                title               =     "{{__('Send invoice to buyer ?')}}"
                                                                wrong               = "{{__('Something went wrong.')}}"
                                                                later               = "{{__('Please try again later.')}}" >
                                                       
                                                            
                                                            {{__('send invoice to buyer')}}
                                                            
                                                        </button>
                                                        @endif
                                                    @endif
                                                @elseif($invoice_period ==  'weekly_invoices')
                                                 
                                                   
                                                    @if(\Carbon\Carbon::now()->weekOfYear.'-'.\Carbon\Carbon::now()->year   !=  ltrim($date,0))
                                                        @if (Auth::guard('seller')->user()->can('send_invoice', App\Invoice::class))
                                                        <button class="btn btn-sm btn-primary send_invoice"
                                                                buyer_company_id    =   "{{$orders[0]->buyer_company_id}}"
                                                                seller_company_id   =   "{{$orders[0]->seller_company_id}}"
                                                                period              =   "{{$date}}"
                                                                invoice_freq        =   "{{$orders[0]->invoice_freq}}"
                                                                order_ids           =   "{{json_encode($order_ids[$invoice_period][$date][$company])}}"
                                                                title               =     "{{__('Send invoice to buyer ?',['buyer'=>$company])}}"
                                                                wrong               = "{{__('Something went wrong.')}}"
                                                                later               = "{{__('Please try again later.')}}" >
                                                            
                                                            {{__('send invoice to buyer')}}
                                                            
                                                        </button>
                                                        @endif
                                                    @else
                                                        <button class="btn btn-sm btn-primary disabled">
                                                            {{ __('current '.   str_replace('i','y',str_replace('ly','',explode('_',$invoice_period)[0])))  }}
                                                           
                                                        </button>
                                                    @endif
                                                @elseif($invoice_period ==  'monthly_invoices')
                                                    @if(\Carbon\Carbon::now()->shortEnglishMonth.'-'.\Carbon\Carbon::now()->year   !=  ltrim($date,0))
                                                        @if (Auth::guard('seller')->user()->can('send_invoice', App\Invoice::class))
                                                        <button class="btn btn-sm btn-primary send_invoice"
                                                                buyer_company_id    =   "{{$orders[0]->buyer_company_id}}"
                                                                seller_company_id   =   "{{$orders[0]->seller_company_id}}"
                                                                period              =   "{{$date}}"
                                                                invoice_freq        =   "{{$orders[0]->invoice_freq}}"
                                                                order_ids           =   "{{json_encode($order_ids[$invoice_period][$date][$company])}}"
                                                                title               =     "{{__('Send invoice to buyer ?',['buyer'=>$company])}}"
                                                                wrong               = "{{__('Something went wrong.')}}"
                                                                later               = "{{__('Please try again later.')}}" >
                                                            
                                                            {{__('send invoice to buyer')}}
                                                            
                                                        </button>
                                                        @endif
                                                    @else
                                                        <button class="btn btn-sm btn-primary disabled">
                                                            {{ __('current '.  str_replace('i','y',str_replace('ly','',explode('_',$invoice_period)[0]))) }}
                                                        </button>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </li>
                                    <div id="collapse{{ hash('adler32',array_values( $company_totals[$invoice_period][$date][$company])[0])}}"
                                         class="collapse"
                                         aria-labelledby="{{ hash('adler32',array_values( $company_totals[$invoice_period][$date][$company])[0])}}"
                                         data-parent="#invoices">
        
                                    <div class="card-body">
                                        
                                        @foreach($orders as $order)
                                            
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="/order/{{$order->id}}/{{$order->$owner_company_id}}">
                                                    {{--{{$company}} ---}}
                                                    {{__(str_replace('_',' ',$order->department))}}  -
                                                    {{$order->created_at}}</a>
                                                <p>{{$order->total_order_cost}}  {{$order->currency}}</p>
                                            </li>
                                        @endforeach
                                    
                                    </div>
                                    </div>
                                @endforeach
                            </div> {{--end of accordion--}} </div>
                            @endforeach
                        @endforeach
                    </ul>
                
                </div>
            
            </div>
        </div>
    </div>
@endsection


