@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    @include('includes.which_left_side')
                </div>
            </div>
            <div class="col-md-9">
                @include('includes.which_nav')
                
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            
            <div class="col-sm-12">
                <div class="card text-center mt-2 ">
                    <div class="card-header text-dark bg-transparent">
                       
                       
                        <div class="row d-flex justify-content-between align-items-center">
                            <p class="card-title ml-2">{{__('Select day of your delivery!')}}</p>
                            <div class=" mb-2 mt-2 ">
                                @foreach($weekOfdays as $day    =>  $dates)
                                   
                                   
                                    <button class="btn btn-sm btn-outline-success text-dark "
                                            id="delivery_date"
                                            title="{{$day}}"
                                            delivery_date = "{{ $dates['en_timestamp']  }}"
                                            day_num = "{{ $dates['day_num']  }}"
                                            buyer_company_id    =   "{{$buyer_company_id}}"
                                            department    =   "{{$department}}"
                                    >
                                       
                                        {{ $dates['display_date'] }}
                                        
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center border-danger text-dark bg-transparent">
                        
                        <h5 class="card-title">{{__('Online ordering.')}}
                            @if(empty($cheap_products))
                                {{__('You have no active sellers at the moment !')}}
                            @else
                                {{__('Your current suppliers :')}}
                            @endif
                      
                        </h5>
                        
                    </div>
                    
                    <div class="card-body bg-transparent">
                        @if(!empty($cheap_products))
                     
                            @foreach($seller_companies as $seller_company)
                            <div class="row d-flex justify-content-center align-items-center">
                                <div class="col d-flex justify-content-between align-items-center">
                                    <a href="/price_list/{{$department}}/{{$seller_company->id}}/{{$buyer_company_id}}">
                                        {{$seller_company->seller_company_name }}
                                    </a>
                                     {{$seller_company->seller_name }}
                                        <br><small>{{__('tel:')}}</small><small class="text-primary">
                                            {{$seller_company->seller_phone_number }}
                                        </small>
                                </div>
                                <div class="col">
                                   {{__('Delivery days:')}}
                                        <small class="text-primary">
                                            @if(sizeof(json_decode($seller_company->delivery_days,true)) == 7 )
                                                {{__('Every day ;-)')}}
                                            @else
                                                @foreach(json_decode($seller_company->delivery_days,true) as $key => $day)
                                
                                                    @if($key == sizeof(json_decode($seller_company->delivery_days,true)) - 1 )
                                                        {{__($day)}}
                                                    @else
                                                        {{__($day)}},
                                                    @endif
                                                @endforeach
                                            @endif
                    
                    
                                        </small>
                                   
                                </div>
                                <div class="col">
                                   {{__('Last order at:')}}
                                        <small class="text-primary">{{$seller_company->last_order_at }}</small>
                                    
                                </div>
                            </div>
                            @endforeach
                            <hr>
                        @endif
                        
                        @if(empty($cheap_products))
                            <div class="card-header d-flex justify-content-between align-items-center bg-danger  border-light text-light">
                                <h5 class="card-title">{{__('You have no active sellers at the moment !')}}</h5>
                            </div>
                        @else
                             <form id="online_order" class="{{$department}} " enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                <div class="row">
                                    @if($unavailable_products)
                                        <div class="card col-md-12">
                                            <div class="bg-danger text-light card-header mb-2">
                                                 {{__('Unavailable products : ')}}
                                                <small>{{__('Sellers don\'t have it in the stock, or you have disabled products manually !')}}</small>
                                            </div>
                                            <div class="card-body">
                                                @foreach($unavailable_products as $product)
                                                        {{$product}}
                                                    @if(!$loop->last)
                                                        {{','}}
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <?php $key=0; ?>
                                    @foreach($cheap_products as $product=>$data)
                                        <?php $key++; ?>
                                        <div class="card col-md-2 {{$key.str_replace(' ','_',$product)}}">
                                            <div class="row ">
                                                {{--ORDERING PER PACK--}}
                                                @if(isset($data['box_size']) && $data['box_size'] != 0)
                                                    <div class="col-md-12  d-flex justify-content-around align-items-center">
                                                        <div class="col-md-7">
                                                            {!! Form::selectRange($product, 0, 10, null, ['name'=>$product,'placeholder' => '','class' => 'select_mini product','id'=> $key.str_replace(' ','_',$product)]);!!}
                                                        </div>
                                                        <div class="col">
                                                            <span class="small text-primary">{{__('packs ')}}</span>
                                                        </div>
                                                        @if($data['type_brand'] || $data['additional_info'])
                                                            <div class="col">
                                                                <a href="#" class="badge badge-primary">{{__('i')}}</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12  d-flex justify-content-between align-items-center">
                                                        <label  class="col-form-label col-form-label-sm  text-primary">{{$product}} </label><small class="text-dark"> {{$data['price_per_kg']}}{{__('EUR per kg')}}</small>
                                                        <small class="text-danger">{{(float)$data['box_size']*(float)$data['price_per_kg']}} {{__('EUR per')}} {{$data['box_size']}} {{__('kg')}}</small>
                                                    </div>
                                                    {{--ORDERING PER KG--}}
                                                @else
                                                    <div class="col-md-12  d-flex justify-content-around align-items-center">
                                                        <div class="col-md-7">
                                                            {!! Form::selectRange($product, 0, 10, null, ['name'=>$product,'placeholder' => '','class' => 'select_mini product','id'=> $key.str_replace(' ','_',$product)]);!!}
                                                        </div>
                                                        <div class="col">
                                                            <span class="small ">{{__('kg\'s')}} </span>
                                                        </div>
                                
                                                        @if($data['type_brand'] || $data['additional_info'])
                                                            <div class="col">
                                        
                                                                <a href="#"
                                                                   class="badge badge-primary product_info"
                                                                   data-toggle="tooltip"
                                                                   data-html="true"
                                                                   title="{{!empty($data['type_brand']) ? "<em>type/brand : " : ''}}
                                                                   {{!empty($data['type_brand']) ? $data['type_brand'] : ''}}
                                                                   {{!empty($data['type_brand']) ? "</em> <br/> " : ''}}
                                                                   {{!empty($data['additional_info']) ? "<em>info : " : ''}}
                                                                   {{!empty($data['additional_info']) ? $data['additional_info'] : ''}}
                                                                   {{!empty($data['additional_info']) ? "</em>" : ''}}"
                                        
                                                                >
                                                                    &nbsp;i&nbsp;
                                                                </a>
                                    
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12  d-flex justify-content-between align-items-center">
                                                        <label  class="col-form-label col-form-label-sm text-primary">{{$product}}</label><small class="text-dark"> {{$data['price_per_kg']}} {{__('EUR per kg')}}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                 <button id="order_placed"
                                         class="btn btn-sm btn-outline-success"
                                         department="{{$department}} "
                                         buyer_company_id="{{$buyer_company_id}}"
                                         no_product = "{{__('Did you select at least one product ?')}}"
                                         moment = "{{__('Just a moment !')}}"
                                         ordering ="{{__('Your Order is being processed.')}}"
                                         wrong = "{{__('Something went wrong.')}}"
                                         later   = "{{__('Please try again later.')}}"
                                 
                                 >
                                     
                                     {{__('Order')}}
                                 </button>
                             </form>
                            
                                </div>
                           
                        @endif
                    </div>
                </div>
            </div>
        </div>
   
@endsection
