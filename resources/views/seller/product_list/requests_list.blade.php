
<div class="card ">
    <div class="card-header bg-secondary text-light">
        
        {{__('Product list requests')}}  @if(!$product_list_requests) <code>{{__('no  requests')}}</code>  @endif
    </div>
    <div class="list-group{{-- d-flex justify-content-between align-items-center--}} ">
        
        @if($product_list_requests)
          {{-- {{dd($product_list_requests)}}--}}
            @foreach($product_list_requests as $department => $request)
                <li class="list-group-item list-group-item-info">
                    {{str_replace('_',' ',$department)}}
                </li>
                @foreach($request as $stocklist_request)
                    <li class="list-group-item list-group-item-action"  >
                        
                        {{-- <form  action="{{ URL::to('send_stocklist_to_seller',[ $company->id/*company_id*/,$stocklist_request->department_name/*department_name*/, $stocklist_request->seller_company_id/*seller_id*/ ]) }}"  method="post" enctype="multipart/form-data">
							 {{ csrf_field() }}
							 <button  class="btn btn-outline-success btn-sm" title="Respond to Stock List Request from $seller_company_names[$stocklist_request->seller_company_id]">Respond to{{$seller_company_names[$stocklist_request->seller_company_id]}} </button>
						 </form>--}}
                      {{--  {{dd($company_names)}}--}}
                        @if($stocklist_request->responded == 1) {{-- PRODUCT LIST IS  AVAILABLE--}}
                            @if($stocklist_request->requester == 'buyer_owner' || $stocklist_request->requester == 'buyer_buyer')
                                <div class="d-flex justify-content-between align-items-center no-gutters">
                                   
                                        <p>{{$stocklist_request->buyer_company_name}}</p>
                                        <p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>
                                    <a href="/pricing/{{$stocklist_request->buyer_company_id}}/{{$stocklist_request->department}}/{{$stocklist_request->seller_company_id}}">
                                        <p class="text-success" >
                                           {{-- Requested by
                                            @if($user_names['seller'][$stocklist_request->responder_user_id] == \Auth::guard('seller')->user()->name)
                                                You
                                            @else
                                                {{$user_names['seller'][$stocklist_request->responder_user_id]}}
                                            @endif
                                            {{\Carbon\Carbon::parse($stocklist_request->created_at)->diffForHumans() }} &#10004;--}}
                                            <small>{{__('AVAILABLE')}} {{\Carbon\Carbon::parse($stocklist_request->responded_at)->diffForHumans() }} &#10004;</small>
                                        </p>
                                    </a>
                                </div>
                            
                            
                            
                            @elseif($stocklist_request->requester == 'seller_owner' || $stocklist_request->requester == 'seller_seller')
                                <div class="d-flex justify-content-between align-items-center no-gutters">
                                    <p>{{$stocklist_request->buyer_company_name}}</p>
                                    <p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>
    
                                    <a href="/pricing/{{$stocklist_request->buyer_company_id}}/{{$stocklist_request->department}}/{{$stocklist_request->seller_company_id}}">
                                    <p class="text-success" >
    
                                       {{-- {{$user_names['buyer'][$stocklist_request->responder_user_id]}}
                                        responded--}}
                                        
                                        <small>{{__('AVAILABLE')}} {{\Carbon\Carbon::parse($stocklist_request->responded_at)->diffForHumans() }} &#10004;</small>
                                    </p>
                                    </a>
                                </div>
                            @endif
                        @elseif($stocklist_request->requested == 1 )
                            @if($stocklist_request->requester == 'buyer_owner' || $stocklist_request->requester == 'buyer_buyer')
                                <div class="d-flex justify-content-between align-items-center no-gutters">
                                    <p>{{$stocklist_request->buyer_company_name}}</p>
                                    <p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>
                                    <a href="/pricing/{{$stocklist_request->buyer_company_id}}/{{$stocklist_request->department}}/{{$stocklist_request->seller_company_id}}">
                                    <p class="text-success" >
                                        <small>{{__('AVAILABLE')}} {{\Carbon\Carbon::parse($stocklist_request->responded_at)->diffForHumans() }} &#10004;</small>
                                        {{--Requested by BUYER :
                                        {{$user_names['buyer'][$stocklist_request->requester_user_id]}}
                                        {{\Carbon\Carbon::parse($stocklist_request->created_at)->diffForHumans() }} &#10004;--}}
                                    </p>
                                    </a>
                                </div>
    
   
    
                            
                        @elseif($stocklist_request->requester == 'seller_owner' || $stocklist_request->requester == 'seller_seller')
                                <div class="d-flex justify-content-between align-items-center no-gutters">
                                    <p  title="{{$stocklist_request->buyer_company_name}} {{__('wants your')}} {{str_replace('_', ' ',$stocklist_request->department)}} {{__('product list !')}}">{{$company_names[$stocklist_request->buyer_company_id]}}</p>
                                    <p>{{str_replace('_', ' ',$stocklist_request->department)}}</p>
                                    <form  action="{{ URL::to('product_list_request') }}"  method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        
                                        {{ Form::text('buyer_company_id', $stocklist_request->buyer_company_id,['class' => 'd-none']) }}
                                        {{ Form::text('seller_company_id', $stocklist_request->seller_company_id,['class' =>'d-none']) }}
                                        {{ Form::text('department', str_replace(' ','_',$stocklist_request->department),['class' => 'd-none']) }}
                                        
                                        <p class="text-primary" title="{{__('You requested product list from')}} {{$stocklist_request->buyer_company_name}}">
                                            {{__('Requested by')}}
                                            @if($user_names['seller'][$stocklist_request->requester_user_id] == \Auth::guard('seller')->user()->name)
                                                {{__('You')}}
                                            @else
                                                {{$user_names['seller'][$stocklist_request->requester_user_id]}}
                                            @endif
                                            
                                            {{\Carbon\Carbon::parse($stocklist_request->created_at)->diffForHumans() }} &#10004; </p>
                                    </form>
                                </div>
                            
                        @endif
                        @else
                        <div class="d-flex justify-content-between align-items-center no-gutters">
                            <p>{{$stocklist_request->buyer_company_name}}</p>
                            <p>{{str_replace('_', ' ',$stocklist_request->department/*department*/)}}</p>
                            <form  action="{{ URL::to('product_list_request') }}"  method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                
                                {{ Form::text('buyer_company_id', $stocklist_request->buyer_company_id,['class' => 'd-none']) }}
                                {{ Form::text('seller_company_id', $stocklist_request->seller_company_id,['class' =>'d-none']) }}
                                {{ Form::text('department', str_replace(' ','_',$stocklist_request->department),['class' => 'd-none']) }}
                                
                                <button class="btn btn-primary btn-sm" title="{{__('Send Stock List Request to')}} {{$stocklist_request->buyer_company_name}}">{{__('Send Stock List to')}} {{$company_names[$stocklist_request->seller_company_id]}}</button>
                            </form>
                        </div>
                        @endif
            @endforeach
        @endforeach
    @endif
</div>
