@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="col-md-3">
                <div class="list-group">--}}{{-- {{ dd($company->companyName) }}--}}{{--
                                <button type="button" class="list-group-item active"><a style="color: #ffffff" href="{{ url('/department/'.str_replace(' ','_',$searchedDepartment).'/'.$company->id) }}">
                                        {{ isset($company->buyer_company_name) ? __('Back') : __('Create company first')}}</a></button>
                </div>
            </div>--}}
                <div class="col-md-12 ">
                    
                               @component('components.header_with_navigation',['links' =>   ['Sellers','Search_sellers'], 'active'    =>  $active ])
                               @endcomponent
                               
                                @include('includes.feedback')
                                
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-light_green ">
                                        {{__('Ready sellers')}} {{ $ready_sellers['size'] }}
                                    </div>
                                    <ul class="list-group">
                                        @foreach($ready_sellers['companies'] as $department => $locations)
                                            <li class="list-group-item list-group-item-light_green"> {{str_replace(' ','_',$department) }}
                                                <span class="float-right">  {{ sizeof($locations) }} {{__('Locations')}}</span>
                                            </li>
                                            @foreach($locations as $location => $companies)
                                                <li class="list-group-item text-orange "> {{$location}}
                                                    <span class="float-right">    {{ sizeof($companies) }} {{__('Companies')}}</span>
                                                </li>
                                                @foreach($companies as $company)
                                                    <li class="list-group-item">
                                                        <a href="/buyer/about/{{$company->id}}/seller">{{ $company->name }}</a>
                                                        <span class="badge  float-right">
	                                                 <span class="btn btn-sm btn-light_green text-secondary" >
                                                        {{__('Prices available...')}} &#10004;&#10004;
                                                    </span>
	                                                   
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
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-light" >
                                        {{__('To be sellers')}} {{ $to_be_sellers['size'] }}
                                    </div>
                                    <ul class="list-group">
                                        @foreach($to_be_sellers['companies'] as $department => $locations)
                                            <li class="list-group-item list-group-item-light_green"> {{ str_replace(' ','_',$department) }}
                                                <span class="float-right">  {{ sizeof($locations) }} {{__('Locations')}}</span>
                                            </li>
                                            @foreach($locations as $location => $companies)
                                                <li class="list-group-item text-orange "> {{$location}}
                                                    <span class="float-right">    {{ sizeof($companies) }} {{__('Companies')}}</span>
                                                </li>
                                                @foreach($companies as $company)
                                                
                                                    <li class="list-group-item">
                                                        <a href="/buyer/about/{{$company->id}}/seller">{{ $company->name }}</a>
                                                        <span class="badge  float-right">
	                                               @if($company->requested &&$company->responded && $company->requester == 'seller')
                                                                <span class="btn btn-sm btn-light_green text-secondary"
                                                                 >
                                                        {{__('Product list available to seller...')}} &#10004;
                                                    </span>
                                                     @elseif($company->requested  && $company->requester == 'buyer')
                                                                <span class="btn btn-sm btn-light_green text-secondary"
                                                                   >
                                                        {{__('Product list available to seller...')}} &#10004;
                                                    </span>
                                                     @elseif($company->requested && $company->requester == 'seller')
                                                                    <button class="btn btn-sm btn-outline-success text-danger"
                                                                            id="product_list_request"
                                                                            data-delivery_location_id     =       "{{$company->delivery_location_id}}"
                                                                            title                 =       "{{__('Send product List to ')}} {{ $company->name }}"

                                                                            data-buyer_company_id          =       "{{session()->get('company_id')}}"
                                                                            data-buyer_email               =       "{{$company->buyer_email}}"
                                                                            data-buyer_company_name        =       "{{$company->buyer_company_name}}"

                                                                            data-country                   =       "{{$company->buyer_country}}"
                                                                            data-county                    =       "{{$company->buyer_county}}"
                                                                            data-county_l4                 =       "{{$company->buyer_county_l4}}"

                                                                            data-seller_company_id         =       "{{$company->id}}"
                                                                            data-seller_email              =       "{{$company->seller_email}}"
                                                                            data-seller_company_name       =       "{{$company->seller_company_name}}"

                                                                            data-department                =       "{{str_replace(' ','_',$department)}}"
                                                                            data-wrong                     =       "{{__('Something went wrong.')}}"
                                                                            data-later                     =       "{{__('Please try again later.')}}">
        
                                                                 {{__('Send product list')}}
    
                                                             </button>
                                                     @endif
	                                                   
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
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-orange text-secondary">
                                        {{__('Undiscovered sellers')}} <span class="float-right">   {{ $undiscovered_sellers['size'] }} {{__('Departments')}}</span>
                                    </div>
                                    <ul class="list-group">
                                        @foreach($undiscovered_sellers['companies'] as $department => $locations)
                                            <li class="list-group-item list-group-item-light_green"> {{str_replace(' ','_',$department)}}
                                                <span class="float-right">  {{ sizeof($locations) }} {{__('Locations')}}</span>
                                            </li>
                                            @foreach($locations as $location => $companies)
                                                <li class="list-group-item text-orange "> {{$location}}
                                                    <span class="float-right">    {{ sizeof($companies) }} {{__('Companies')}}</span>
                                                </li>
                                                @foreach($companies as $company)
                                                    <li class="list-group-item">
                                                        <a href="/buyer/about/{{$company->id}}/seller">{{ $company->name }}</a>
                                                        <span class="badge  float-right">
	                                                                    <button class="btn btn-sm btn-outline-success text-danger"
                                                                                id="product_list_request"
                                                                                data-delivery_location_id     =       "{{$company->delivery_location_id}}"
                                                                                title                 =       "{{__('Send product List to ')}} {{ $company->name }}"
                                                                                
                                                                                data-buyer_company_id          =       "{{session()->get('company_id')}}"
                                                                                data-buyer_email               =       "{{$company->buyer_email}}"
                                                                                data-buyer_company_name        =       "{{$company->buyer_company_name}}"

                                                                                data-country                   =       "{{$company->buyer_country}}"
                                                                                data-county                    =       "{{$company->buyer_county}}"
                                                                                data-county_l4                 =       "{{$company->buyer_county_l4}}"

                                                                                data-seller_company_id         =       "{{$company->id}}"
                                                                                data-seller_email              =       "{{$company->seller_email}}"
                                                                                data-seller_company_name       =       "{{$company->seller_company_name}}"

                                                                                data-department                =       "{{str_replace(' ','_',$department)}}"
                                                                                data-wrong                     =       "{{__('Something went wrong.')}}"
                                                                                data-later                     =       "{{__('Please try again later.')}}" >
    
                                                                {{__('Send product list')}}
                                                                
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
                        </div>
                </div>
            </div>
        </div>
   

@endsection
