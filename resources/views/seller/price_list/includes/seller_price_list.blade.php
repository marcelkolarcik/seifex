@if(isset($seller_price_list) )
    @foreach($seller_price_list as $product_name => $product_data)
        @if(isset($info->new_products) && in_array($product_data['product_name'],array_keys($info->new_products)))
            <tr class="bg-light_green ">
        @else
            <tr>
                {{ Form::text('products['.$product_name.'][old_hash_name]' ,isset($product_data['old_hash_name']) ? $product_data['old_hash_name']: $product_name,
																				   array(
																				   'class' =>    'd-none',
																				 'readonly' )) }}
        @endif
              
                @foreach($product_data as $product_detail => $product_desc)
                 
                    @if($product_data['product_name']   != '')
                     
                        {{--{{ Form::text('products['.$product_name.']['.$product_detail.']',$product_desc) }}--}}
                        @if($product_detail == 'price_per_kg' || $product_detail == 'product_name')
                
                         
                            @if(($product_detail == 'price_per_kg') && $product_desc == '')
                                <td >{{ Form::text('products['.$product_name.']['.$product_detail.']', $product_desc,
                                                            array('required'=>'required',
                                                             isset($info->new_products[$product_data['product_name']]) ? $info->new_products[$product_data['product_name']] :'' ,
                                                            'class' => 'form-control form-control-sm ',
                                                            'placeholder'=>__('required'))) }}</td>
                            
                            @elseif($product_detail == 'product_name')
                                <td >{{ Form::text('products['.$product_name.']['.$product_detail.']', $product_desc,
                                                            array('required'=>'required',
                                                             isset($info->new_products[$product_data['product_name']]) ? $info->new_products[$product_data['product_name']] :'' ,
                                                            'class' => 'form-control form-control-sm ',
                                                            'readonly' => 'readonly')) }}</td>
                            @else
                                <td >{{ Form::text('products['.$product_name.']['.$product_detail.']', $product_desc,
                                                            array('required'=>'required',
                                                            isset($info->new_products[$product_data['product_name']]) ? $info->new_products[$product_data['product_name']] :'' ,
                                                            'class' => 'form-control form-control-sm ',
                                                             'placeholder'=>__('required'))) }}</td>
                            
                            @endif
                        @else
                            @if($product_detail == 'unset' && $product_desc > 0)
                                <td>
                                    {{ Form::text('products['.$product_name.']['.$product_detail.']', $product_desc,
								array(
								 isset($info->new_products[$product_data['product_name']]) ? $info->new_products[$product_data['product_name']] :'' ,
								'class' => 'form-control form-control-sm bg-warning',
								 'readonly' => 'readonly')) }}</td>
                            @elseif($product_detail == 'unset')
                                <td >{{ Form::text('products['.$product_name.']['.$product_detail.']', $product_desc,
                                                            array( 'class' => 'form-control form-control-sm',
                                                             isset($info->new_products[$product_data['product_name']]) ? $info->new_products[$product_data['product_name']] :'' ,
                                                            'readonly' => 'readonly')) }}</td>
                            @elseif($product_detail != 'old_hash_name')
                              
                                <td >{{ Form::text('products['.$product_name.']['.$product_detail.']', $product_desc,
                                                            array( 'class' => 'form-control form-control-sm',
                                                             isset($info->new_products[$product_data['product_name']]) ? $info->new_products[$product_data['product_name']] :'' )) }}</td>
                            @endif
                            
                        @endif
                    @endif
                @endforeach
            </tr>
            @endforeach
        @endif
