<div class="list-group list-group-horizontal-sm" >
    <a class="list-group-item list-group-item-action pt-1 pb-1 bg-secondary text-light"  >{{__('Orders')}}</a>
    <a class="list-group-item list-group-item-action pt-1 pb-1 bg-grey-300 text-grey-800"  >  {{__('some_other')}}</a>
    <a class="list-group-item list-group-item-action pt-1 pb-1 bg-grey-300 text-grey-800"  >{{ $buyer_company_name }}  </a>
</div>
<!-- List group -->
<div class="list-group list-group-horizontal-sm" id="myList" role="tablist">
    <a class="list-group-item list-group-item-action active" data-toggle="list" href="#home" role="tab">Home</a>
    <a class="list-group-item list-group-item-action" data-toggle="list" href="#profile" role="tab">Profile</a>
    <a class="list-group-item list-group-item-action" data-toggle="list" href="#messages" role="tab">Messages</a>
    <a class="list-group-item list-group-item-action" data-toggle="list" href="#settings" role="tab">Settings</a>
</div>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="home" role="tabpanel">...Home</div>
    <div class="tab-pane" id="profile" role="tabpanel">...Profile</div>
    <div class="tab-pane" id="messages" role="tabpanel">...Messages</div>
    <div class="tab-pane" id="settings" role="tabpanel">...Settings</div>
</div>
@if(!empty($orders))
     @if($type  ==   'buyer')
        @foreach($orders as $department => $department_orders )
            
            <div class="card ">
                <div class="list-group list-group-horizontal-sm mt-2 mb-1" >
                    <a class="list-group-item list-group-item-action pt-1 pb-1 bg-primary text-light disabled mr-1" >{{  __(str_replace('_',' ',$department)) }} </a>
                    @foreach($department_orders as $type=>$type_orders)
                        @if($type == 'received')
                            @if(isset($department_orders['received_commented']))
                                <a class="list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#{{$department.$type}}" role="tab">
                                    {{__($type)}} {{'( '.sizeof($type_orders)}} <small class="text-danger">{{'#'.sizeof($department_orders['received_commented']).__(' commented ')}}</small>{{')'}}
                                </a>
                            @else
                                <a class="list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#{{$department.$type}}" role="tab">
                                    {{__($type)}} {{'( '.sizeof($type_orders)}} {{')'}}
                                </a>
                            @endif
        
                        @elseif($type != 'received_commented')
                            @if($type == 'delivered')
                                <a class="list-group-item list-group-item-action active pt-1 pb-1" data-toggle="list" href="#{{$department.$type}}" role="tab">
                                    {{__($type)}} {{'( '.sizeof($type_orders)}} <small class="text-danger">{{__('to confirm')}}</small> )
                                </a>
                            @else
                                <a class="list-group-item list-group-item-action {{$type == 'new' ? ' active':''}}  pt-1 pb-1" data-toggle="list" href="#{{$department.$type}}" role="tab">
                                    {{__($type)}} {{'( '.sizeof($type_orders).' )'}}
                                </a>
                            @endif
                        @endif
                    @endforeach
                </div>
                <div class="tab-content">
                @foreach($department_orders as $type=>$type_orders)
       
                    <div class=" mb-3 tab-pane {{$type == 'new' ? ' active': ''}}"
                         id="{{$department.$type}}" role="tabpanel">
    
                        <div class="list-group list-group-horizontal-sm text-primary" >
                            <a class="list-group-item list-group-item-action  pt-1 pb-1 bg-primary text-light disabled"  >{{__('From')}}</a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Ordered')}}</a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Seen')}}</a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Dispatched')}}</a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Delivered')}}</a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Received')}}</a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Commented')}}</a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Cost')}}</a>
                        </div>
                            @foreach($type_orders as $order)
                                @if($order->buyer_confirmed_delivery_at == null && $type == 'delivered')
                                <div class="list-group list-group-horizontal-sm bg-warning" >
                                @else
                                   <div class="list-group list-group-horizontal-sm  " >
                                 @endif
                                       <a class="list-group-item list-group-item-action pt-1 pb-1 {{$loop->first && $type == 'new' ? ' bg-light_green ': ''}}" href="{{ URL::to('order',[$order->id,$order->$ow_company_id ]) }}" >
                                       {{$order->$opposite_company_name}}  <small class="text-primary">{{__('view')}}</small>
                                       </a>
                                       <a class="list-group-item list-group-item-action pt-1 pb-1"  >
                                           <small> {{ \Carbon\Carbon::parse($order->created_at)->isoFormat('MMM/D H:m') }}</small>
                                       </a>
                                       <a class="list-group-item list-group-item-action pt-1 pb-1"  >
                                            @if($order->checked_at != null)
                                                <small> {{ \Carbon\Carbon::parse($order->checked_at)->isoFormat('MMM/D H:m') }} </small>
                                
                                            @else
                                                <span class="badge text-danger">  &#10008;</span>
                                            @endif
                                        </a>
                                       <a class="list-group-item list-group-item-action pt-1 pb-1"  >
                                            @if($order->prepped_at != null)
                                                <small> {{ \Carbon\Carbon::parse($order->prepped_at)->isoFormat('MMM/D H:m') }} </small>
                                
                                            @else
                                                <span class="badge text-danger">  &#10008;</span>
                                            @endif
                                        </a>
                                       <a class="list-group-item list-group-item-action pt-1 pb-1"  >
                                            @if($order->delivered_at != null)
                                                <small> {{ \Carbon\Carbon::parse($order->delivered_at)->isoFormat('MMM/D H:m') }}</small>
                                
                                            @else
                                                <span class="badge text-danger">  &#10008;</span>
                                            @endif
                                        </a>
                                       <a class="list-group-item list-group-item-action pt-1 pb-1"  >
                                            @if($order->buyer_confirmed_delivery_at != null)
                                                <small> {{ \Carbon\Carbon::parse($order->buyer_confirmed_delivery_at)->isoFormat('MMM/D H:m') }}</small>
                                
                                            @else
                                                <span class="badge text-danger">  &#10008;</span>  @if($order->delivered_at != null) <small>{{__('to confirm')}}</small>@endif
                                            @endif
                                        </a>
                                       <a class="list-group-item list-group-item-action pt-1 pb-1"  >
                                            @if($order->comment != null)
                                                <span class="badge text-success"> &#10004;</span>
                                            @else
                                                <span class="badge text-danger">  &#10008;</span>
                                            @endif
                                        </a>
                                       <a class="list-group-item list-group-item-action pt-1 pb-1"  >
                                          <span class="text-primary">{{$order->total_order_cost}} {{!isset($order->currency) ?:$order->currency }}</span>
                                       </a>
                                   </div>
                                    @endforeach
                           
                    </div>
                @endforeach
                </div>
                
                
                
                {{--///////////////////////////////////////////////////////////////////////////////////////
                //////////////////////////////////////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////////////////////////////////////////--}}
                {{--<div class="accordion" id="department_orders">--}}
                    {{--<div class="card-header bg-transparent">--}}
                    {{--@foreach($department_orders as $type=>$type_orders)--}}
                            {{--@if($type == 'received')--}}
                                {{--@if(isset($department_orders['received_commented']))--}}
                            {{--<button class="btn btn-outline-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapse{{$department.$type}}" aria-expanded="true" aria-controls="collapse{{$department.$type}}">--}}
                                {{--{{__($type)}} {{'( '.sizeof($type_orders)}} <small class="text-danger">{{'#'.sizeof($department_orders['received_commented']).__(' commented ')}}</small>{{')'}}--}}
                            {{--</button>--}}
                                {{--@else--}}
                                    {{--<button class="btn btn-outline-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapse{{$department.$type}}" aria-expanded="true" aria-controls="collapse{{$department.$type}}">--}}
                                        {{--{{__($type)}} {{'( '.sizeof($type_orders)}} {{')'}}--}}
                                    {{--</button>--}}
                                {{--@endif--}}
        {{----}}
                            {{--@elseif($type != 'received_commented')--}}
                                {{--@if($type == 'delivered')--}}
                                {{--<button class="btn btn-outline-success btn-sm" type="button" data-toggle="collapse" data-target="#collapse{{$department.$type}}" aria-expanded="true" aria-controls="collapse{{$department.$type}}">--}}
                                    {{--{{__($type)}} {{'( '.sizeof($type_orders)}} <small class="text-danger">{{__('to confirm')}}</small> )--}}
                                {{--</button>--}}
                                {{--@else--}}
                                    {{--<button class="btn btn-outline-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapse{{$department.$type}}" aria-expanded="true" aria-controls="collapse{{$department.$type}}">--}}
                                        {{--{{__($type)}} {{'( '.sizeof($type_orders).' )'}}--}}
                                    {{--</button>--}}
                                {{--@endif--}}
                            {{--@endif--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
        {{----}}
                    {{--@foreach($department_orders as $type=>$type_orders)--}}
                        {{----}}
                        {{--<div id="collapse{{$department.$type}}" class="--}}{{--{{$type=='new' ? 'collapse show': 'collapse'}}--}}{{--collapse " aria-labelledby="{{$department.$type}}" data-parent="#department_orders">--}}
                            {{--<table class="table table-sm table-striped table-bordered">--}}
                                {{--<thead>--}}
                                {{--<tr>--}}
                                    {{--<th>{{__('Company Name')}}</th>--}}
                                    {{--<th>{{__('Ordered')}}</th>--}}
                                    {{--<th>{{__('Seen')}}</th>--}}
                                    {{--<th>{{__('Dispatched')}}</th>--}}
                                    {{--<th>{{__('Delivered')}}</th>--}}
                                    {{--<th>{{__('Received')}}</th>--}}
                                    {{--<th>{{__('Commented')}}</th>--}}
                                {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<tbody>--}}
                                    {{--@foreach($type_orders as $order)--}}
                                        {{--@if($order->buyer_confirmed_delivery_at == null && $type == 'delivered')--}}
                                        {{--<tr class="bg-warning text-danger">--}}
                                        {{--@else--}}
                                            {{--<tr>--}}
                                        {{--@endif--}}
                                            {{--<td><a href="{{ URL::to('order',[$order->id,$order->$ow_company_id ]) }}">--}}
                                                   {{----}}
                                                    {{--{{$order->$opposite_company_name}}--}}
                                                    {{--( {{$order->total_order_cost}} {{!isset($order->currency) ?:$order->currency }})</a></td>--}}
                                            {{--<td> <small> {{ \Carbon\Carbon::parse($order->created_at)->isoFormat('MMM/D H:m') }}</small></td>--}}
                                                {{--<td>--}}
                                                    {{--@if($order->checked_at != null)--}}
                                                        {{--<small> {{ \Carbon\Carbon::parse($order->checked_at)->isoFormat('MMM/D H:m') }} </small>--}}
                                                      {{----}}
                                                    {{--@else--}}
                                                        {{--<span class="badge text-danger">  &#10008;</span>--}}
                                                    {{--@endif--}}
                                                {{--</td>--}}
                                                {{--<td>--}}
                                                    {{--@if($order->prepped_at != null)--}}
                                                        {{--<small> {{ \Carbon\Carbon::parse($order->prepped_at)->isoFormat('MMM/D H:m') }} </small>--}}
                                                       {{----}}
                                                    {{--@else--}}
                                                        {{--<span class="badge text-danger">  &#10008;</span>--}}
                                                    {{--@endif--}}
                                                {{--</td>--}}
                                                {{--<td>--}}
                                                    {{--@if($order->delivered_at != null)--}}
                                                        {{--<small> {{ \Carbon\Carbon::parse($order->delivered_at)->isoFormat('MMM/D H:m') }}</small>--}}
                                                        {{----}}
                                                    {{--@else--}}
                                                        {{--<span class="badge text-danger">  &#10008;</span>--}}
                                                    {{--@endif--}}
                                                {{--</td>--}}
                                                {{--<td>--}}
                                                    {{--@if($order->buyer_confirmed_delivery_at != null)--}}
                                                        {{--<small> {{ \Carbon\Carbon::parse($order->buyer_confirmed_delivery_at)->isoFormat('MMM/D H:m') }}</small>--}}
                                                        {{----}}
                                                    {{--@else--}}
                                                        {{--<span class="badge text-danger">  &#10008;</span>  @if($order->delivered_at != null) <small>{{__('to confirm')}}</small>@endif--}}
                                                    {{--@endif--}}
                                                {{--</td>--}}
                                                {{--<td>--}}
                                                    {{--@if($order->comment != null)--}}
                                                        {{--<span class="badge text-success"> &#10004;</span>--}}
                                                    {{--@else--}}
                                                        {{--<span class="badge text-danger">  &#10008;</span>--}}
                                                    {{--@endif--}}
                                                {{--</td>--}}
                                            {{--</tr>--}}
                                        {{--</tr>--}}
                                    {{--@endforeach--}}
                                {{--</tbody>--}}
                            {{--</table>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
        @endforeach
            </div>
     
     
    @elseif($type  ==   'seller')
    
    
    
     
   
@foreach($orders as $department  =>  $locations)
    
         <div class="card ">
             <div class="accordion" id="department_orders">
                {{-- <div class="card-header bg-info text-light">
                     {{str_replace('_',' ',$department)}}
                 </div>--}}
             
                 <div class="card-header bg-transparent">
            @foreach($locations as $location  =>  $types)
               
                         <span class="btn btn-sm btn-outline-secondary disabled" > {{$location}}</span> :     {{__(str_replace('_',' ',$department))}}
                 @foreach($types as $type=>$type_orders)
                            
                            @if($type == 'received')
                                @if(isset($types['received_commented']))
                                     <button class="btn btn-outline-primary btn-sm order_type float-right m-1" type="button" data-toggle="collapse" data-target="#collapse{{hash('adler32',$location).hash('adler32',$department).hash('adler32',$type)}}" aria-expanded="true" aria-controls="collapse{{hash('adler32',$location).hash('adler32',$department).hash('adler32',$type)}}">
    
                                         {{__($type)}} {{'( '.sizeof($type_orders)}} <small class="text-danger">{{' #'.sizeof($types['received_commented']). __('commented') }}</small>{{')'}}
                                     </button>
                                @endif
                             
                            @elseif($type != 'received_commented')
                                 <button class="btn btn-outline-primary btn-sm order_type float-right m-1" type="button" data-toggle="collapse" data-target="#collapse{{hash('adler32',$location).hash('adler32',$department).hash('adler32',$type)}}" aria-expanded="true" aria-controls="collapse{{hash('adler32',$location).hash('adler32',$department).hash('adler32',$type)}}">
                                     {{__($type)}} {{'( '.sizeof($type_orders).' )'}}
                                </button>
                            @endif
                            
                             
                        
                       
                        @if($loop->last) <hr> @endif
                     <div id="collapse{{hash('adler32',$location).hash('adler32',$department).hash('adler32',$type)}}" class="{{--{{$type=='new' ? 'collapse show': 'collapse'}}--}} collapse" aria-labelledby="{{hash('adler32',$location).hash('adler32',$department).hash('adler32',$type)}}" data-parent="#department_orders">
                         <table class="table table-sm table-striped table-bordered">
                             <thead>
                             <tr>
                                 <th>{{__('Company Name')}}</th>
                                 <th>{{__('Ordered')}}</th>
                                 <th>{{__('Seen')}}</th>
                                 <th>{{__('Dispatched')}}</th>
                                 <th>{{__('Delivered')}}</th>
                                 <th>{{__('Confirmed by buyer')}}</th>
                                 <th>{{__('Comment')}}</th>
                             </tr>
                             </thead>
                             <tbody>
                             @foreach($type_orders as $order)
                                 
                                 
                                 <tr>
                                     <td><a href="{{ URL::to('order',[$order->id,$order->$ow_company_id ]) }}">
                                             {{$order->$opposite_company_name}}
                                             {{$order->total_order_cost}} {{!isset($order->currency) ?:$order->currency }}</a></td>
                                     <td><small>{{ \Carbon\Carbon::parse($order->created_at)->isoFormat('MMM/D H:m') }}</small></td>
                                     <td>
                                         @if($order->checked_at != null)
                                             <small> {{ \Carbon\Carbon::parse($order->checked_at)->isoFormat('MMM/D H:m') }}</small>
                                            
                                         @else
                                             <span class="badge text-danger">  &#10008;</span>
                                         @endif
                                     </td>
                                     <td>
                                         @if($order->prepped_at != null)
                                             <small> {{ \Carbon\Carbon::parse($order->prepped_at)->isoFormat('MMM/D H:m') }}</small>
                                            
                                         @else
                                             <span class="badge text-danger">  &#10008;</span>
                                         @endif
                                     </td>
                                     <td>
                                         @if($order->delivered_at != null)
                                             <small> {{ \Carbon\Carbon::parse($order->delivered_at)->isoFormat('MMM/D H:m') }}</small>
                                            
                                         @else
                                             <span class="badge text-danger">  &#10008;</span>
                                         @endif
                                     </td>
                                     <td>
                                         @if($order->buyer_confirmed_delivery_at != null)
                                             <small> {{ \Carbon\Carbon::parse($order->buyer_confirmed_delivery_at)->isoFormat('MMM/D H:m') }}</small>
                                            
                                         @else
                                             <span class="badge text-danger">  &#10008;</span>
                                         @endif
                                     </td>
                                     <td>
                                         @if($order->comment != null)
                                             <span class="badge text-success"> &#10004;</span>
                                         @else
                                             <span class="badge text-danger">  &#10008;</span>
                                         @endif
                                     </td>
                                 </tr>
                                 
                                 
                                 
                                 
                                 
                             @endforeach
                             </tbody>
                         </table>
                     </div>
                 @endforeach
              @endforeach
             </div>
             
             </div>
         </div>
@endforeach
    @endif
@else
                    <div class="list-group list-group-horizontal-sm" >
                        <a class="list-group-item list-group-item-action pt-1 pb-1 bg-warning text-grey-800 mt-2"  >{{__('No orders yet.')}}</a>
                    </div>
@endif
  

