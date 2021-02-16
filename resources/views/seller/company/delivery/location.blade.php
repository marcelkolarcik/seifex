@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
         
            <div class="col-md-12 ">
                @include('includes.feedback')
                <div class="list-group list-group-horizontal-sm mb-1 " >
                    <a class="list-group-item list-group-item-action bg-secondary text-light_green pt-1 pb-0  " href="\delivery_locations" >
                        {{__('Delivery locations')}}
                        
                    </a>
                <a class="list-group-item list-group-item-action disabled bg-grey-300 pt-1 pb-0  "  >
                   
                    @foreach($delivery_location['path'] as $location    =>  $path)
            
                        <label for="location" class="">
                            {{ $location }}
                        </label>
                        <label for="path" class="small text-grey-800">
                            {{ $path }}
                        </label>
        
                    @endforeach
                        <hr>
                        {{__('Delivery days : ')}}
                    @foreach($delivery_days as $day)
                        {{$day}}
                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                </a>
                    <a class="list-group-item list-group-item-action bg-grey-300 disabled pt-1 pb-0 "  >
                        {{__('Department')}} :
                        {{ __(\App\Services\StrReplace::currency_underscore($department) )  }}
                        <hr>
                        {{__('Revenue : ')}}
                        @foreach($revenue as $currency => $sum)
                            {{$currency}} : {{$sum}}
                        @endforeach
                    </a>
                </div>
                <div class="list-group list-group-horizontal-sm mb-sm-2" >
                    <a class="list-group-item list-group-item-action active pt-1 pb-1 " data-toggle="list" href="#buyers" role="tab" >
                        {{ 'active buyers' }}
                    </a>
                    <a class="list-group-item list-group-item-action pt-1 pb-1 " data-toggle="list" href="#sellers" role="tab" >
                        {{ 'active sellers' }}
                    </a>
                    <a class="list-group-item list-group-item-action pt-1 pb-1 " data-toggle="list" href="#staff" role="tab" >
                        {{ 'staff' }}
                    </a>
                </div>
                
                
                <div class="tab-content">
                <div class="tab-pane active" id="buyers" role="tabpanel">
                  
                    @foreach($buyers_in_location as $buyer)
                        <div class="list-group list-group-horizontal-sm mb-sm-1" >
                       <a class="list-group-item list-group-item-action pt-1 pb-1 "  >
                         {{$buyer['company_name']}}
                       </a>
                       <a class="list-group-item list-group-item-action pt-1 pb-1 "  >
                        @if(isset($buyer['revenue']))
                            @foreach($buyer['revenue'] as $currency    =>  $revenue)
                            {{__('Revenue :')}}    {{ $revenue  }}  {{$currency}}
                            @endforeach
                        @endif
                       </a>
                        </div>
                    @endforeach
                   
                </div>
               
                <div class="tab-pane" id="sellers" role="tabpanel">
                    @foreach($active_sellers_in_location as $staff_id => $seller)
                        <div class="list-group list-group-horizontal-sm mb-sm-1" >
                            <a class="list-group-item list-group-item-action pt-1 pb-1 "  >
                        {{ $seller['details']['staff_name']  }}
                                @if($seller['details']['role']    ==  'seller_owner')
                                    <label for="owner" class="small">
                                        (  {{__('owner')}}  )
                                    </label>
                                @endif
                            </a>
                    @if(isset($seller['revenue']))
                    @foreach($seller['revenue'] as $currency    =>  $revenue)
                           <a class="list-group-item list-group-item-action pt-1 pb-1 "  >
                               {{__('Revenue :')}}    {{ $revenue  }}  {{$currency}}
                           </a>
                    @endforeach
                    @endif
                       
                      
                        <br>
                        </div>
                    @endforeach
                </div>
                <div class="tab-pane" id="staff" role="tabpanel">
                    <div class="row">
                    
                   
                    @foreach($staff_in_location as $role    => $staff)
                        <div class="col-md-6">
                        
                       
                       @if($role == 'seller_seller')
                           {{__('Sellers :')}}
                       @elseif($role == 'seller_delivery')
                           {{__('Delivery : ')}}
                       @endif
                        <hr>
                    @foreach($staff as $single)
                        {{$single['staff_name']}} <br>
                    @endforeach
                        </div>
                    @endforeach
                    </div>
                </div>
                
                </div>
           
            </div>
        </div>
    </div>
@endsection
