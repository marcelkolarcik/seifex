@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="col-md-3 ">--}}
                {{--@include('includes.seller.left_side')--}}
            {{--</div>--}}
            <div class="col-md-12 ">
                <div class="card bg-secondary  border-light text-light">
                    @include('includes.feedback')
                    @if (Auth::guard('seller')->user()->can('seller_coordinate_requests', App\ProductList::class))
                    <form  action="{{ URL::to('buyers',$seller_company_id) }}"  method="get" enctype="multipart/form-data">
	                <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="row">
			                <div class="col">
                                @include('includes.forms.locations')
			                </div>
			                <div class="col ">
                                <label for="departmentName">{{__('Department')}}</label>
                                {{ Form::select('department',array_unique($departments),str_replace('_',' ',$searched_department),['required' => 'required','class' => 'form-control','placeholder' => __('Select department')]) }}
                            </div>
                            <div class="col">
                                <label for="departmentName">{{__('Refine')}} {{__('Search')}}</label>
                                <button id="submit" class="btn btn-primary  form-control">{{__('Search')}}</button>
			                </div>
                        
                        </div>
                       
	                </div>
                    </form>
                    @endif
                    
                    {{--SEARCH RESULTS--}}
                    @if(request()->country)
                                @if(count($outside_buyers) > 0 )
                            @if(session('expanded') != true)
                                <div class="card bg-info">
                                    <div class="card-body ">
                                        <h6 class="card-subtitle mb-2 ">{{__('Buyers available')}} :</h6>
                                    @foreach($outside_buyers as $buyer)
                                                    <li class="list-group-item list-group-item-light">
                                                        {{ $buyer->buyer_company_name}}
                                                    </li>
                                    @endforeach
                                    </div>
                                    <div class="card-footer bg-info ">
                                        {{__('You need to expand your location for')}}
                                        <mark> {{ __(str_replace('_',' ',$searched_department)) }} </mark>{{__('if you don\'t deliver there yet !')}}<hr>
                                        <div class="d-flex justify-content-between">
                    
                                            <h6 class="text-warning">
                                               {{$searched_location}}
                                            </h6>
                                            <form  action="{{ URL::to('expand_delivery_locations') }}"
                                                   method="post" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                        
                                                {{ Form::text('department', $searched_department,['class' => 'd-none']) }}
                                                {{ Form::text('country',  request()->country,['class' => 'd-none']) }}
                                                @if(isset(request()->county)  && request()->county != 'Please select your county')
                                                    {{ Form::text('county', request()->county, ['class' => 'd-none']) }}
                                                @else
                                                    {{ Form::text('county', '', ['class' => 'd-none']) }}
                                                @endif
                                                @if(isset(request()->county)  && request()->county != 'Please select your location')
                                                    {{ Form::text('county_l4', request()->county_l4, ['class' => 'd-none']) }}
                                                @else
                                                    {{ Form::text('county_l4', '', ['class' => 'd-none']) }}
                                                @endif
                                                
                                                {{ Form::text('seller_company_id', $seller_company_id, ['class' => 'd-none']) }}
                                                <button class="btn btn-primary ">{{__('Expand now !')}}</button>
                                            </form>
                                        </div>
                                        
                                    </div>
                                   
                                </div>
                            @endif
                                @else
                                    <div class="list-group-item list-group-item-warning">
                                       <h6>{{__('No results')}}</h6>
                                    </div>
                                @endif
                    @endif
                    {{--END OF SEARCH RESULTS--}}
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light_green ">
                                {{__('Ready buyers')}} {{ $ready_buyers['size'] }}
                            </div>
                                <ul class="list-group">
                                    @foreach($ready_buyers['companies'] as $department => $locations)
                                        <li class="list-group-item list-group-item-light_green"> {{str_replace('_',' ',$department)}}
                                            <span class="float-right">  {{ sizeof($locations) }} {{__('Locations')}}</span>
                                           
                                        </li>
                                        @foreach($locations as $location => $companies)
                                            <li class="list-group-item text-orange "> {{$location}}
                                                <span class="float-right">    {{ sizeof($companies) }} {{__('Companies')}}</span>
                                            </li>
                                            @foreach($companies as $company)
                                                <li class="list-group-item">
                                                    <a href="/seller/about/{{$company->id}}/buyer">{{ $company->name }}</a>
                                                    <span class="small float-md-right">
                                                     @if($company->requester == 'seller')
                                                            {{__('Managed by : ')}}
                                                                @if(($company->price_list_seller_id == \Auth::guard('seller')->user()->id))
                                                                <a class="btn btn-sm btn-light_green text-secondary"
                                                                   href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">
													    {{__('You')}} &#10004;
												            </a>
                                                                @else
                                                                {{$company->seller_name}}
                                                                @endif
                                                            <br>
                                                         @if(\Auth::guard('seller')->user()->role == 'seller_owner')
                                                                <a class="btn btn-sm btn-light_green text-secondary"
                                                                   href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">
													        {{__('Price list')}} &#10004;
												            </a>
                                                         @endif
														  
													   @elseif($company->requester == 'buyer')
														   {{__('Requested by : ')}} {{$company->name}} <br>
														   {{__('Managed by : ')}}
                                                            @if(($company->requester_user_id == \Auth::guard('seller')->user()->id))
                                                                <a class="btn btn-sm btn-light_green text-secondary"
                                                                   href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">
													    {{__('You')}} &#10004;
												            </a>
                                                            @else
                                                                {{$company->seller_name}}
                                                            @endif
                                                            <br>
                                                            @if(\Auth::guard('seller')->user()->role == 'seller_owner')
                                                                <a class="btn btn-sm btn-light_green text-secondary"
                                                                   href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">
													        {{__('Price list')}} &#10004;
												            </a>
                                                            @endif
													   @endif
													   
											   </span>
												   {{--<span class="badge  float-right">--}}
													   {{--@if(($company->price_list_seller_id == \Auth::guard('seller')->user()->id) ||   \Auth::guard('seller')->user()->role == 'seller_owner')--}}
														   {{--<a class="btn btn-sm btn-light_green text-secondary"--}}
															  {{--href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">--}}
													   {{--{{__('Priced...')}} &#10004;&#10004;--}}
													   {{--</a>--}}
													   {{--@endif--}}
												   {{----}}
													  {{----}}
											   {{--</span>--}}
												   <br>
												   <small>
													   @foreach(json_decode($company->address,true) as $line)
														   {{$line}}
														   @if(!$loop->last)
															   {{' - '}}
														   @endif
													   @endforeach
												   </small>
											   </li>
			   
										   @endforeach
									   @endforeach
								   @endforeach
							   </ul>
						  
					   </div>
				   </div>
				   <div class="col-md-4">
					   <div class="card">
						   <div class="card-header bg-primary text-light" >
							   {{__('To be buyers')}} {{$to_be_buyers['size']}}
						   </div>
						   <ul class="list-group">
							   @foreach($to_be_buyers['companies'] as $department => $locations)
								   <li class="list-group-item list-group-item-light_green"> {{str_replace('_',' ',$department) }}
									   <span class="float-right">  {{ sizeof($locations) }} {{__('Locations')}}</span>
									  
								   </li>
								   @foreach($locations as $location => $companies)
									   <li class="list-group-item text-orange "> {{$location}}
										   <span class="float-right">    {{ sizeof($companies) }} {{__('Companies')}}</span>
									   </li>
									   @foreach($companies as $company)
										   <li class="list-group-item">
											   <a href="/seller/about/{{$company->id}}/buyer">{{ $company->name }}</a>
											   <span class="small float-md-right">
													@if($company->requester == 'seller')
                                                       {{__('Managed by : ')}}
                                                       @if(($company->requester_user_id == \Auth::guard('seller')->user()->id) )
                                                           {{__('You')}} &#10004;
                                                          
                                                       @else
                                                           {{$company->seller_name}}
                                                       @endif
                                                       @if($company->responded == 1 && ($company->requester_user_id == \Auth::guard('seller')->user()->id
														  || \Auth::guard('seller')->user()->role == 'seller_owner'))
                                                           <a class="btn btn-sm btn-light_green text-secondary"
                                                              href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">
                                                                 {{__('Product list')}} &#10004;
												            </a>
                                                       @else
                                                           <a class="btn btn-sm btn-light_green text-secondary"
                                                              href="#" title="{{__('waiting for buyer to respond')}}">
                                                               {{__('waiting...')}}
												            </a>
                                                       @endif
                                                     
                                                       {{--@if(\Auth::guard('seller')->user()->role == 'seller_owner'  && $company->responded == 1)--}}
                                                           {{--<br>--}}
                                                           {{--<a class="btn btn-sm btn-light_green text-secondary"--}}
                                                              {{--href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">--}}
													        {{--{{__('Product list')}} &#10004;--}}
												            {{--</a>--}}
                                                       {{--@endif--}}
													@elseif($company->requester == 'buyer')
													   {{__('Requested by : ')}} {{$company->name}} <br>
													   {{__('Managed by : ')}}
                                                       @if(($company->requester_user_id == \Auth::guard('seller')->user()->id))
                                                          
                                                           <a class="btn btn-sm btn-light_green text-secondary"
                                                              href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">
													    {{__('You')}} &#10004;
												            </a>
                                                       @else
                                                           {{$company->seller_name}}
                                                       @endif
                                                       @if(\Auth::guard('seller')->user()->role == 'seller_owner')
                                                           <br>
                                                           <a class="btn btn-sm btn-light_green text-secondary"
                                                              href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">
													        {{__('Product list')}} &#10004;
												            </a>
                                                       @endif
												   @endif
											   </span>
											  
											   {{--<span class="badge  float-right">--}}
												   {{----}}
												  {{----}}
													   {{--@if(  $company->responded && $company->requester == 'seller'--}}
															   {{--&&  ($company->seller_id == \Auth::guard('seller')->user()->id)--}}
													  {{--||\Auth::guard('seller')->user()->role == 'seller_owner' && $company->responded && $company->requester == 'seller'--}}
													  {{--|| $company->requested && $company->requester == 'buyer' && $company->seller_id == \Auth::guard('seller')->user()->id--}}
													  {{--|| $company->requested && $company->requester == 'buyer' && $company->seller_id == null)--}}
														   {{----}}
														   {{--<a class="btn btn-sm btn-light_green text-secondary"--}}
															  {{--href="/pricing/{{ $company->id }}/{{$department }}/{{ $seller_company_id }}">--}}
													   {{--{{__('Product list available')}} &#10004;--}}
												   {{--</a>--}}
													{{--@endif--}}
											   {{----}}
												  {{----}}
													  {{----}}
											   {{--</span>--}}
											   <br>
											   <small>
												   @foreach(json_decode($company->address,true) as $line)
													   {{$line}}
													   @if(!$loop->last)
														   {{' - '}}
													   @endif
												   @endforeach
											   </small>
										   </li>
			   
									   @endforeach
								   @endforeach
							   @endforeach
						   </ul>
					   </div>
				   </div>
				   <div class="col-md-4">
					   <div class="card">
						   <div class="card-header bg-orange text-secondary">
							   {{__('Undiscovered buyers')}} <span class="float-right">   {{$undiscovered_buyers['size']}}</span>
						   </div>
								   <ul class="list-group">
									   @foreach($undiscovered_buyers['companies'] as $department => $locations)
										   <li class="list-group-item list-group-item-light_green"> {{ str_replace('_',' ',$department) }}
											 <span class="float-right">  {{ sizeof($locations) }} {{__('Locations')}}</span>
											 
										   </li>
										   @foreach($locations as $location => $companies)
											   <li class="list-group-item text-orange "> {{$location}}
												   <span class="float-right">    {{ sizeof($companies) }} {{__('Companies')}}</span>
											   </li>
											   @foreach($companies as $company)
												   <li class="list-group-item">
													   <a href="/seller/about/{{$company->id}}/buyer">{{ $company->name }}</a>
													 
														   <span class="badge  float-right">
																	   {{--<form  action="{{ URL::to('product_list_request') }}"  method="post" enctype="multipart/form-data">--}}
																		   {{--{{ csrf_field() }}--}}
	   {{----}}
																		   {{--<button class="btn btn-sm btn-secondary btn-outline-light_green"--}}
																				   {{--title="{{ $company->name }} -- {{ explode('|',str_replace('_',' ',$department)) [1] }} Stock List">--}}
																			   {{--{{__('Request product list')}}--}}
																		   {{--</button>--}}
	   {{----}}
																		   {{--{{ Form::text('buyer_company_id', $company->id,['class' => 'd-none']) }}--}}
																		   {{--{{ Form::text('seller_company_id', $seller_company_id,['class' =>'d-none']) }}--}}
																		   {{--{{ Form::text('department',  explode('|',$department) [1] ,['class' => 'd-none']) }}--}}
																	   {{--</form>--}}
															  
																<button class="btn btn-sm btn-outline-success text-danger"
																		id="product_list_request"
																		data-delivery_location_id      =       "{{$company->delivery_location_id}}"
																		title                          =       "{{__('Request product list')}} : {{ $company->name }}"
																		
																		data-buyer_company_id          =       "{{$company->id}}"
																		data-buyer_email               =       "{{$company->buyer_email}}"
																		data-buyer_company_name        =       "{{$company->buyer_company_name}}"

																		data-country                   =       "{{$company->country}}"
																		data-county                    =       "{{$company->county}}"
																		data-county_l4                 =       "{{$company->county_l4}}"
																		
																		data-seller_company_id         =       "{{session()->get('company_id')}}"
																		data-seller_email              =       "{{$company->seller_email}}"
																		data-seller_company_name       =       "{{$company->seller_company_name}}"
																		
																		data-department                =       "{{str_replace(' ','_',$department)}}"
																		data-wrong                     =       "{{__('Something went wrong.')}}"
																		data-later                     =       "{{__('Please try again later.')}}"
																>
	   
																{{__('Request product list')}}
   
															</button>
															
															   </span>
													
													  
													   <br>
													   <small>
														   @foreach(json_decode($company->address,true) as $line)
															   {{$line}}
															   @if(!$loop->last)
																   {{' - '}}
															   @endif
														   @endforeach
													   </small>
												   </li>
												   
											   @endforeach
										   @endforeach
									   @endforeach
								   </ul>
					   </div>
				   </div>
				  
			   </div> {{--end of div.row--}}
		   </div>{{--end of div.col-md-9--}}
	   </div>{{--end of div.row--}}
   </div> {{--end of div.container--}}
	  

@endsection
