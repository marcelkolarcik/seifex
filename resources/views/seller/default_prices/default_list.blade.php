@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9 ">
           <div class="card ">
                    <div class="card-header">
                        <h4><b>{{$b_company->buyer_company_name}}</b></h4> <small>{{str_replace('_',' ',session('department'))}}</small>
                        <hr/>
                        <ul>
                            <li>
                                {{__('Fill in the prices and sizes where applicable.')}}
                            </li>
                            <li>
                                {{__('Save it for buyer to use your prices !')}}
                            </li>
                            <li>
                                {{__('If you don\'t have certain product, write Zero')}} <code>0</code>{{__(', in')}} <b>{{__('Price per kg')}}</b> {{__('Column !')}}
                            </li>
                        </ul>
                        <hr/>
                        {{__('We will add the prices,that you are applying here, to your default price list for future use.')}}
                    </div>
           </div>
          <div class="card ">
               <div class="card-header">

                @if(isset($first_timer))
               <a href="{{ url('/seller_applying_default_prices',[session('seller_company_id'),session('department'),session('buyer_company_id')]) }}" class="btn btn-outline-success btn-sm">{{__('Apply your default prices')}}</a>
                @elseif(!isset($first_timer) && isset($seller_price_list))
                       <a href="{{ url('/seller_applying_default_prices',[session('seller_company_id'),session('department'),session('buyer_company_id')]) }}" class="btn btn-outline-success btn-sm">{{__('Apply your default prices')}}</a> {{__('Edit or apply your prices')}}
                 @endif
                @if (!empty($show_multi) || !empty($show_single))
                        <a href="{{ url('/seller_applying_default_prices',[session('seller_company_id'),session('department'),session('buyer_company_id')]) }}" class="btn btn-outline-success btn-sm">{{__('Apply your default prices')}}</a> {{__('Your default prices')}} <code>{{__('You can change them !')}}</code>
                @elseif(!empty($show_multi) && !isset($first_timer))
                        <a href="{{ url('/seller_applying_default_prices',[session('seller_company_id'),session('department'),session('buyer_company_id')]) }}" class="btn btn-outline-success btn-sm">{{__('Apply your default prices')}}</a>    {{__('Edit or apply your prices')}}
                @endif
                    @include('seller.default_prices.includes.modal_default_prices')
                 <form  action="{{ URL::to('save_sellers_price_list') }}"  method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                <div class="card-body">

                 <div id="product_prices" >
                  <table class="table table-condensed table-bordered" id="multi_seller_prices">
                      <tr>
                          <td ><b>{{__('Product')}} {{__('name')}}</b></td>
                          <td ><b>{{__('Price per kg')}}</b><br/><small class="text-danger"> {{__('( no currency sign )')}}</small></td>
                          <td >{{__('Type / Brand')}}<br/><small>{{__('( if you have more of same product name )')}}</small></td>
                          <td >{{__('Size of unit in')}} {{__('kg/l')}}<br/><small>{{__('( if applicable / no kg/l sign )')}}</small></td>
                          <td >{{__('Price per unit')}}<br/><small>{{__('( if applicable )')}}</small></td>
                          <td >{{__('Additional info')}}<br/><small>{{__('( if applicable )')}}</small></td>
                          <td >{{__('Unset')}}<br/><small>{{__('( num of buyers unset this product )')}}</small></td>
                      </tr>
                      <div class="text-primary "><p>{{__('Select from multiple choices')}}</p></div>
                    @if (!empty($show_multi))
                                      @foreach($show_multi as $product_name => $products_multi)

                                          <?php
                                           $select_data=[];
                                            foreach($products_multi as $key=>$product_data)
                                                    {
                                                        if(!empty($product_data['type_brand'])) {$type_brand = ' | '.$product_data['type_brand'] ;} else{$type_brand = '';}
                                                        if(!empty($product_data['box_size'])) {$box_size = ' | '.$product_data['box_size'].__(' kg/l size') ;} else{$box_size = '';}
                                                        if(!empty($product_data['box_price'])) {$box_price = ' | '.$product_data['box_price'].__(' unit price ') ;} else{$box_price = '';}
                                                        if(!empty($product_data['additional_info'])) {$additional_info = ' | '.$product_data['additional_info'] ;} else{$additional_info = '';}
                                                        
                                                        $select_data[ str_replace(' ','_',$product_data['product_name'])
                                                            .'|'.$product_data['price_per_kg']
                                                            .'|'.$product_data['type_brand']
                                                            .'|'.$product_data['box_size']
                                                            .'|'.$product_data['box_price']
                                                            .'|'.$product_data['additional_info']
                                                            .'|'.$product_data['unset']] =
	
	                                                        $product_data['product_name']
                                                        .' '.$product_data['price_per_kg'].__(' per kg ')
                                                        .$type_brand
                                                        .$box_size
                                                        .$box_price
                                                        .$additional_info
                                                        ;
                                                    }
                                            ?>

                                              <tr>
                                            
                                              <select required id="{{str_replace(' ','_',$product_name)}}" class="form-control form-control-sm multi btn-outline-danger" >
                                                         <option value="0">{{__('Select price for')}} {{$product_name}}</option>
                                                      @foreach($select_data as $short=>$long)
                                                          <option value={{$short}} > {{$long}}</option>
                                                      @endforeach
                                              </select>
                                         </tr> <br/>
                                      @endforeach
                                          <br/>
                    @endif
                    
                    @if (!empty($show_single))




                          @foreach($show_single as $product_name => $product_data)
                             
                              <tr >
                                  <td >{{ Form::text($product_name.'|product_name', $product_name, array('required'=>'required','class' => 'form-control form-control-sm','readonly' => 'readonly')) }}</td>
                                  @foreach($product_data as $product_detail => $product_desc)
                                      {{-- {{$product_detail .' '. $product_desc}}--}}
                                  @if( $product_detail != 'product_name')
                                      @if($product_detail == 'price_per_kg' && $product_desc == '')
                                              <td >{{ Form::text($product_name.'|'.$product_detail, $product_desc, array('required'=>'required', 'class' => 'form-control form-control-sm bg-warning','placeholder'=>__('required'))) }}</td>
                                      @elseif($product_detail == 'price_per_kg' )
                                          <td >{{ Form::text($product_name.'|'.$product_detail, $product_desc, array('required'=>'required', 'class' => 'form-control form-control-sm')) }}</td>
                                      @else
                                          @if($product_detail == 'unset' && $product_desc > 0)
                                              <td class="table-warning">
                                                  {{ Form::text($product_name.'|'.$product_detail, $product_desc, array( 'class' => 'form-control form-control-sm','readonly' => 'readonly')) }}</td>
                                          @elseif($product_detail == 'unset')
                                              <td >
                                                  {{ Form::text($product_name.'|'.$product_detail, $product_desc, array( 'class' => 'form-control form-control-sm','readonly' => 'readonly')) }}</td>
                                              
                                          @else
                                              <td >{{ Form::text($product_name.'|'.$product_detail, $product_desc, array( 'class' => 'form-control form-control-sm')) }}</td>
                                          @endif
                                          {{-- <td>{{ Form::text($product_name.'|'.$product_detail, $product_desc, array( 'class' => 'form-control')) }}</td>--}}
                                      @endif
                                  @endif
                                  @endforeach
                                  
                                 
                              </tr>

                           @endforeach
                    @endif
                    @if(isset($seller_price_list) )
                          @foreach($seller_price_list as $product_name => $product_data)
                              <tr>
                                {{--  <td >{{ Form::text($product_name.'|product_name', $product_name, array('required'=>'required','class' => 'form-control form-control-sm')) }}</td>--}}
                                 
                                 
                                  @foreach($product_data as $product_detail => $product_desc)
                                     
                                      @if($product_detail == 'price_per_kg' || $product_detail == 'product_name')
                                          @if(($product_detail == 'price_per_kg') && $product_desc == '')
                                              <td >{{ Form::text($product_name.'|'.$product_detail, '', array('required'=>'required', 'class' => 'form-control form-control-sm bg-warning', 'placeholder'=>__('required'))) }}</td>
                                          @else
                                              <td >{{ Form::text($product_name.'|'.$product_detail, $product_desc, array('required'=>'required', 'class' => 'form-control form-control-sm ','placeholder'=>__('required'))) }}</td>
                                          @endif
                                      @else
                                          @if($product_detail == 'unset' && $product_desc > 0)
                                              <td>{{ Form::text($product_name.'|'.$product_detail, $product_desc, array( 'class' => 'form-control form-control-sm btn-danger text-danger ','readonly' => 'readonly')) }}</td>
                                          @elseif($product_detail == 'unset')
                                              <td >{{ Form::text($product_name.'|'.$product_detail, $product_desc, array( 'class' => 'form-control form-control-sm','readonly' => 'readonly')) }}</td>
                                          @else
                                              <td >{{ Form::text($product_name.'|'.$product_detail, $product_desc, array( 'class' => 'form-control form-control-sm')) }}</td>
                                          @endif
                                      @endif
                                     
                                  @endforeach
                                 
                              </tr>
                          @endforeach
                      @endif

                  </table>
                     
                     <div id="update_prices">
                         {!! Form::submit(__('Save your changes'), ['name' => 'submitbutton' , 'class' => 'btn btn-success  form-control']) !!}
                     </div>
                 </div>
                 </div>
                </form>
                <div class="footer">
                 @include('includes.uploadFeedback')
                </div>
          </div>
        </div>
    </div>
</div>
</div>
@endsection
