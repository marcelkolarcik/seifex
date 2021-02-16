
<div class="card ">
    <div class="card-header bg-secondary text-light">
        
        {{__('Product list requests')}}  @if(!$product_list_requests) <code>{{__('no  requests')}}</code>  @endif
    </div>
    <div class="list-group {{--d-flex justify-content-between align-items-center --}}">
        
     @if($product_list_requests)
        
        @foreach($product_list_requests as $department => $request)
                <li class="list-group-item list-group-item-info"  >
                    {{str_replace('_',' ',$department)}}
                </li>
            @foreach($request as $stocklist_request)
              
              <li class="list-group-item list-group-item-action"  >
                @if($stocklist_request->responded == 1)
                   
                                        @if($stocklist_request->requester == 'buyer_owner' || $stocklist_request->requester == 'buyer_buyer')
                                            <div class="d-flex justify-content-between align-items-center no-gutters">
                                                <p>{{$stocklist_request->seller_company_name}}</p>
                                               {{-- <p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>--}}
                                                <p class="text-success" >
                                                   <small>{{__('AVAILABLE TO SELLER')}} {{\Carbon\Carbon::parse($stocklist_request->responded_at)->diffForHumans() }} &#10004;</small>
                                                </p>
                                            </div>
                                        @elseif($stocklist_request->requester == 'seller_owner' || $stocklist_request->requester == 'seller_seller')
                                            <div class="d-flex justify-content-between align-items-center no-gutters">
                                                <p>{{$stocklist_request->seller_company_name}}</p>
                                               {{-- <p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>--}}
                                                <p class="text-success" >
                                                   
                                                
                                                    <small>{{__('AVAILABLE TO SELLER')}} {{\Carbon\Carbon::parse($stocklist_request->responded_at)->diffForHumans() }} &#10004;</small>
                                                </p>
                                            </div>
                                        @endif
                @elseif($stocklist_request->requested == 1 )
                                        @if($stocklist_request->requester == 'buyer_owner' || $stocklist_request->requester == 'buyer_buyer')
                                            <div class="d-flex justify-content-between align-items-center no-gutters">
                                                <p>{{$stocklist_request->seller_company_name}}</p>
                                                {{--<p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>--}}
                                                <p class="text-success" >
                                                    
                                                    {{__('Request sent by')}}
                                                            @if($user_names['buyer'][$stocklist_request->requester_user_id] == \Auth::guard('buyer')->user()->name)
                                                                {{__('You')}}
                                                            @else
                                                                {{$user_names['buyer'][$stocklist_request->requester_user_id]}}
                                                            @endif
                                                    
                                                    {{\Carbon\Carbon::parse($stocklist_request->created_at)->diffForHumans() }} &#10004;
                                                </p>
                                            </div>
                                            
                                            </div>
                        
                                        @elseif($stocklist_request->requester == 'seller_owner' || $stocklist_request->requester == 'seller_seller')
                                            <div class="d-flex justify-content-between align-items-center no-gutters">
                                                <p  title="{{$stocklist_request->seller_company_name}} {{__('wants your')}} {{str_replace('_', ' ',$stocklist_request->department)}} {{__('product list!')}}">
                                                    {{$stocklist_request->seller_company_name}}</p>
                                               {{-- <p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>--}}
                                                {{--<form  action="{{ URL::to('/product_list_request') }}"  method="post" enctype="multipart/form-data">--}}
                                                    {{--{{ csrf_field() }}--}}
                                                    {{----}}
                                                    {{--{{ Form::text('buyer_company_id', $stocklist_request->buyer_company_id,['class' => 'd-none']) }}--}}
                                                    {{--{{ Form::text('seller_company_id', $stocklist_request->seller_company_id,['class' =>'d-none']) }}--}}
                                                    {{--{{ Form::text('department', str_replace(' ','_',$stocklist_request->department),['class' => 'd-none']) }}--}}
                                                    {{----}}
                                                    {{--<button class="btn btn-primary btn-sm" title="{{__('Send product list to')}} {{$stocklist_request->seller_company_name}}">--}}
                                                        {{--{{__('Send product list to')}} {{$stocklist_request->seller_company_name}}--}}
                                                    {{--</button>--}}
                                                {{--</form>--}}
                                             
                                                <button class="btn btn-sm btn-outline-success text-danger"
                                                        id="product_list_request"
                                                        data-delivery_location_id     =       "{{$stocklist_request->delivery_location_id}}"
                                                        title                 =       "  {{__('Send product list to')}} {{$stocklist_request->seller_company_name}}"
                                                        data-buyer_company_id     =       "{{$stocklist_request->buyer_company_id}}"
                                                        data-seller_company_id      =       "{{$stocklist_request->seller_company_id}}"
                                                        data-department            =       "{{str_replace(' ','_',$stocklist_request->department)}}"
                                                        data-wrong                 =       "{{__('Something went wrong.')}}"
                                                        data-later                 =       "{{__('Please try again later.')}}" >
    
                                                    {{__('Send product list to')}} {{$stocklist_request->seller_company_name}}
    
                                                </button>
                                            </div>
                                        @endif
                
                @else
                                            <div class="d-flex justify-content-between align-items-center no-gutters">
                                                <p>{{$stocklist_request->seller_company_name}}</p>
                                               {{-- <p>{{str_replace('_', ' ',$stocklist_request->department/*department*/)}}</p>--}}
                                                <form  action="{{ URL::to('/product_list_request') }}"  method="post" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                            
                                                    {{ Form::text('buyer_company_id', $stocklist_request->buyer_company_id,['class' => 'd-none']) }}
                                                    {{ Form::text('seller_company_id', $stocklist_request->seller_company_id,['class' =>'d-none']) }}
                                                    {{ Form::text('department', str_replace(' ','_',$stocklist_request->department),['class' => 'd-none']) }}
                                                    
                                                    <button class="btn btn-primary btn-sm" title="{{__('Send product list to')}} {{$stocklist_request->seller_company_name}}">{{__('Send product list to')}} {{$company_names[$stocklist_request->seller_company_id]}}</button>
                                                </form>
                                            </div>
                
                @endif
                </li>
               
            @endforeach
        @endforeach
    @endif
</div>
