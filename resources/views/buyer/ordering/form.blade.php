
<div  class="card bg-light_green col-md-12">
    <div class="card-header bg-secondary text-light">
        <!-- Modal BTN-->
        <span id="orders_icon" class="text-light  invisible "   data-toggle="modal" data-target="#orders_holder">
                       <button class="btn btn-sm btn-primary" title="{{__('Preview your order.')}}"   style=" font-family: 'Nunito', sans-serif;font-weight: 200;">
                          <i class="fas fa-shopping-basket"></i> &nbsp;
                           <span id="orders_counter" class="fa-layers-counter" ></span>
                       </button>
        </span>
        <!-- Modal -->
        <div class="modal fade" id="orders_holder" tabindex="-1" role="dialog" aria-labelledby="orders_holderTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-light">
                        <h5 class="modal-title " id="orders_holderTitle">{{__('Ordering')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="placed_orders" class="card-body"></div>
                        <a class="list-group-item d-flex justify-content-between align-items-center" href="">Total
                            <span id="total" class="fa-layers-counter text-dark" ></span>
                        </a>
                    </div>
                   
                    <div class="modal-footer">
                        
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                        <button type="button" class="btn btn-danger" onclick="location.reload();">{{__('Cancel order')}}</button>
                    </div>
                </div>
            </div>
        </div>
      <span> {{__('Ordering')}}{{-- {{__('Please, do not refresh this page, before you order !')}}--}}</span>
    
        <span class = "text-light_green">
           {{__(' You are ordering in :')}}
            {{str_replace('_',' ',array_values($currency) [0]) }}
            </span>
    </div>
    {{--<!-- List group -->--}}
    {{--<div class="list-group list-group-horizontal-sm" id="myList" role="tablist">--}}
    {{--<a class="list-group-item list-group-item-action active" data-toggle="list" href="#form" role="tab">Home</a>--}}
    {{--<a class="list-group-item list-group-item-action" data-toggle="list" href="#currentsellers" role="tab">Profile</a>--}}
    {{--<a class="list-group-item list-group-item-action" data-toggle="list" href="#not_available" role="tab">Messages</a>--}}
    {{--</div>--}}
    {{----}}
    {{--<!-- Tab panes -->--}}
    {{--<div class="tab-content">--}}
    {{--<div class="tab-pane active" id="home" role="tabpanel">...Home</div>--}}
    {{--<div class="tab-pane" id="profile" role="tabpanel">...Profile</div>--}}
    {{--<div class="tab-pane" id="messages" role="tabpanel">...Messages</div>--}}
    {{--<div class="tab-pane" id="settings" role="tabpanel">...Settings</div>--}}
    {{--</div>--}}
    {{--SELLERS--}}
   
        <div class="accordion " id="accordionSellers">
            <div class="card" >
                <button class="btn btn-sm   btn-primary" type="button" data-toggle="collapse" data-target="#sellers" aria-expanded="true" aria-controls="sellers"
                        id="headingOne">
                    {{__('Sellers')}} {{' ( '.sizeof($seller_companies).' ) '}} &nbsp;<i class="fas fa-caret-down"></i>
                </button>
                
                <div id="sellers" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSellers">
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($seller_companies as $seller_company)
                                <li class="list-group-item text-dark">
                                    {{ $seller_company['company_name']  }}  {{ $seller_company['seller_name']  }} {{ $seller_company['seller_phone_number']  }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    
    {{--END OF SELLERS--}}
    {{--UNAVAILABLE PRODUCTS--}}
    @if($unavailable_products)
    <div class="accordion " id="accordionUnavailable">
        <div class="card" >
            <button class="btn btn-sm   btn-danger" type="button" data-toggle="collapse" data-target="#unavailable" aria-expanded="true" aria-controls="unavailable"
                    id="headingOne">
                {{__('Unavailable products')}} {{' ( '.sizeof($unavailable_products).' ) '}} &nbsp;<i class="fas fa-caret-down"></i>
            </button>
            
            <div id="unavailable" class="collapse" aria-labelledby="headingOne" data-parent="#accordionUnavailable">
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($unavailable_products as $product)
                            {{explode('+',$product)[0]}}
                            @if(!$loop->last)
                                {{','}}
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        
        </div>
    </div>
    @endif
    {{--END OF UNAVAILABLE PRODUCTS--}}
</div>
    {{--ORDER FORM--}}
<div class="card bg-warning col-md-12">
    <div class="card-body">
        <form  action="{{ URL::to('/ordering/place_order') }}"  method="post" enctype="multipart/form-data">
            @csrf
            {{--<form id="online_order" class="{{session('details')['department']}} " enctype="multipart/form-data">--}}
                {{--{{ csrf_field() }}--}}
                <div class="row">
                  
                    <?php $key=0; ?>
                    @foreach($cheap_products as $product=>$data)
                        <?php $key++; ?>
                        <div class="col-xl-2 col-lg-2   col-md-3 col-sm-4  img-thumbnail  mb-1">
                            {{--ORDERING PER PACK--}}
                            @if(isset($data['box_size']) && $data['box_size'] != 0)
                                {!! Form::selectRange($product, 1, 10, null, [
                                
                                'data-name'         =>  $product,
                                'data-hash_name'    =>  hash('adler32',$product) ,
                                'data-price_per_kg' =>  $data['price_per_kg'],
                                'data-box_size'     =>  $data['box_size'],
                                'placeholder'       => '',
                                'class'             => 'select_mini product']);!!}
        
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[price_per_kg]' , $data['price_per_kg'] , ['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[sc_id]',$data['sc_id'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[type_brand]',$data['type_brand'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[box_size]',$data['box_size'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[product_code]',$data['product_code'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[hash_name]',$data['hash_name'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[amount]',null,['class'=>'d-none '. hash('adler32',$product)]) !!}
                            
                                
                                <span class="small text-primary">{{__('packs ')}}</span>
                                @if($data['type_brand'] || $data['additional_info'])
                                    <div class="col">
                                        <a href="#" class="badge badge-primary">{{__('i')}}</a>
                                    </div>
                                @endif
                                <label  class="col-form-label col-form-label-sm  text-primary">{{$product}} </label>
                                <small class="text-dark" > {{$data['price_per_kg']}}{{__('EUR per kg')}}</small>
                                <small class="text-danger">{{(float)$data['box_size']*(float)$data['price_per_kg']}}
                                    {{array_key_first($currency)}} {{__('per')}} {{$data['box_size']}} {{__('kg')}}</small>
        
                                {{--ORDERING PER KG--}}
                            @else
                                {!! Form::selectRange($product, 1, 10, null, [
                                
                                'data-name'=>$product,
                                'data-hash_name'    =>  hash('adler32',$product) ,
                                'data-price_per_kg' =>  $data['price_per_kg'],
                                'placeholder' => '',
                                'class' => 'select_mini product']);!!}
        
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[price_per_kg]' , $data['price_per_kg'] , ['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[sc_id]',$data['sc_id'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[type_brand]',$data['type_brand'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[box_size]',$data['box_size'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[product_code]',$data['product_code'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[hash_name]',$data['hash_name'],['class'=>'d-none ']) !!}
                                {!! Form::text('products['.$data['sc_id'].']['.$product.']'.'[amount]',null,['class'=>'d-none '.hash('adler32',$product)]) !!}

                       
                            
                                <span class="small ">{{__('kg\'s')}} </span>
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
                                <label  class="col-form-label col-form-label-sm text-primary">{{$product}}</label>
                                <small class="text-dark"> {{$data['price_per_kg']}} {{array_key_first($currency)}} {{__(' per kg')}}</small>
                            @endif
                          
                        </div>
                    
                    @endforeach

                </div>
            {!! Form::text('department',$department,['class'=>'d-none ']) !!}
            {!! Form::text('bc_id',null,['class'=>'d-none ']) !!}
            {!! Form::submit(__('Place order'), ['name' => 'submitbutton' , 'class' => 'btn btn-light_green form-control form-control-sm ']) !!}
        </form>
    </div>
    {{--<div class="card-footer">--}}
       {{----}}
    {{----}}
        {{--<button id="order_placed"--}}
                {{--class="btn btn-sm btn-outline-success"--}}
                {{--department="{{session('details')['department']}} "--}}
                {{--buyer_company_id="{{session('details')['buyer_company_id']}}"--}}
                {{--no_product = "{{__('Did you select at least one product ?')}}"--}}
                {{--moment = "{{__('Just a moment !')}}"--}}
                {{--ordering ="{{__('Ordering')}}"--}}
                {{--wrong = "{{__('Something went wrong.')}}"--}}
                {{--later   = "{{__('Please try again later.')}}"--}}
    {{----}}
        {{-->--}}
        {{----}}
            {{--{{__('Order')}}--}}
        {{--</button>--}}
      {{----}}
    {{--</div>--}}
</div>
    {{--END OF ORDER FORM--}}
    
    

