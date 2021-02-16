@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
                <div class="col-md-12 ">
                            <div class="card">
                               @component('components.header_with_navigation',['links' =>   ['Sellers','Search_sellers'], 'active'    =>  $active ])
                               @endcomponent
                                
                                <div class="card-body">
                                    @include('includes.feedback')
                                    <form  action="{{ URL::to('search_sellers', $company->id) }}"  method="get" enctype="multipart/form-data">
                                        <div class="container">
                                            <div class="row justify-content-start align-items-start">
                                                    <div class="col">
                                                        <label for="department">{{__('Choose Department')}}</label>
                                                        {{ Form::select('department',$departments,str_replace('_',' ',$searched_department),
                                                        ['class' => 'form-control form-control-sm',
                                                        'placeholder'=>__('Select department'),
                                                        'required'=>'required']) }}
                                                        {{ Form::text('company_id', $company->id ,['class' => 'form-control d-none']) }}
                                                    </div>
                                                    <div class="col">
                                                        @include('includes.forms.locations')
                                                    </div>
                                                    <div class="col">
                                                        <label for="department">&nbsp;</label>
                                                        <button id="submit" class="btn btn-sm btn-primary  form-control  form-control-sm">{{__('Refine Search')}}</button>
                                                    </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                        </div>
                    <div class="row">
                        <div class="col-md-12">
                        @if(isset($welcome))
                                <div class="card bg-grey-800 text-light_green">
                                    <div class="card-header">
                                        <h6 class="card-subtitle mb-2 ">{{__('Happy searching...')}}</h6>
            
                                    </div>
                                </div>
                        @elseif($search_country == 'select' && count($search_sellers)<1)
                                <div class="card bg-grey-800 text-light_green">
                                    <div class="card-header">
                                        <h6 class="card-subtitle mb-2 ">{{__('Your query returned no results, try different location...')}}</h6>
                                        
                                    </div>
                                </div>
                        @elseif(count($search_sellers)<1 && $search_country != 'select')
                       
                           
                            <div class="card bg-warning">
                                <div class="card-header">
                                    <h6 class="card-subtitle mb-2 text-muted">{{__('Your query returned no results, try different location...')}}</h6>
                                    @foreach(explode(' : ',$search_location) as $part)
                                        {{$part}}
                                        @if(!$loop->last)
                                            <br>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{--<div class="card">--}}
                                {{--<div class="card-header">--}}
                                    {{--@foreach(explode(' : ',$search_location) as $part)--}}
                                        {{--{{$part}}--}}
                                        {{--@if(!$loop->last)--}}
                                            {{--<br>--}}
                                        {{--@endif--}}
                                    {{--@endforeach--}}
                                {{--</div>--}}
                            {{--</div>--}}
                       
                              
                         
                                
                                <div class="list-group">
                                    @foreach($search_sellers as $department => $locations)
                                      
                                        @foreach($locations as $location => $companies)
                                           
                                            <div class="card-header bg-light_green text-grey-800">
                                                {{__('Search results for sellers')}}
                                                <span class="float-right">
                                                    {{ sizeof($companies)< 2 ? sizeof($companies).' '.__('Company') :sizeof($companies).' '. __('Companies') }}
                                                </span>
                                                
                                                {{ str_replace('_',' ',$department) }} {{__('in')}}
                                                {{$search_location}}
                                            </div>
                                            @foreach($companies as $company)
                                                <div class="list-group-item">
                                                   
                                                    <a href="/buyer/about/{{$company->id}}/seller">{{ $company->name }}</a>
                                                    
                                                    @if($company->requested &&$company->responded && $company->requester == 'seller')
                                                        <a class="btn btn-sm btn-light_green text-secondary"
                                                           href="#">
                                                            {{__('Product list available to seller...')}} &#10004;
                                                        </a>
                                                    @elseif($company->requested  && $company->requester == 'buyer')
                                                        <a class="btn btn-sm btn-light_green text-secondary"
                                                           href="#">
                                                            {{__('Product list available to seller...')}} &#10004;
                                                        </a>
                                                    @elseif(!$company->requested && !$company->responded)
                                                        <button class="btn btn-sm btn-outline-success text-danger float-right"
                                                                id="product_list_request"
                                                                title                 =       "{{__('Send product List to ')}} {{ $company->name }}"
                                                                seller_company_id     =       "{{$company->id}}"
                                                                buyer_company_id      =       "{{session()->get('company_id')}}"
                                                                department            =       "{{str_replace(' ','_',$department)}}"
                                                                wrong                 =       "{{__('Something went wrong.')}}"
                                                                later                 =       "{{__('Please try again later.')}}" >
            
                                                            {{__('Send product list')}}
        
                                                        </button>
                                                    @endif
                                                    <br>
                                                    <small>
                                                        @foreach(json_decode($company->address,true) as $line)
                                                            {{$line}}
                                                            @if(!$loop->last)
                                                                {{' - '}}
                                                            @endif
                                                        @endforeach
                                                    </small>
                                                    <small>
                                                        {{$company->country}}  {{$company->county}}  {{$company->county_l4}}
                                                    </small>
                                                </div>
                            
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </div>
                          </div>
                    @endif
                        </div>
                </div>
        </div>
    </div>
@endsection
