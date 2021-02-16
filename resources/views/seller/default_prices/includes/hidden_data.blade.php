{{--TO CONVERT PRICES--}}
@if($product_detail == 'price_per_kg')
    
        {{ Form::text($a.'|'.$product_detail, $product_desc,
		array(
		'class' => ' d-none'
	
		)) }}
  
@elseif($product_detail == 'box_price')
   {{ Form::text($a.'|'.$product_detail,$product_desc,
                                                                        array(
                                                                        'class' =>    ' d-none'
                                                                        )) }}
   
@endif
@if($product_detail == 'product_code')
    
        
        {{ Form::text($a.'|'.$product_detail, $product_desc,
		array( 'class' => 'd-none'
		  )) }}
    
   

@elseif($product_detail == 'stock_level')
    @if($product_data['stock_level'] == "")
       
            {{-- SELLER DOESN'T HAVE PRICE FOR THE PRODUCT--}}
            @if($product_data['price_per_kg']  == 0)
                {{ Form::number($a.'|'.$product_detail, null,
				array('class' => ' d-none')) }}
            @else
                {{ Form::number($a.'|'.$product_detail, null,
				array(
				'class' => ' d-none'
				)) }}
            @endif
       
    
    
    @elseif($product_data['low_stock'] >= $product_data['stock_level'])
      
            
            {{ Form::number($a.'|'.$product_detail, $product_desc,
			array(
			'class' => ' d-none')) }}
        
    
    @else
       
            
            {{ Form::number($a.'|'.$product_detail, $product_desc,
			array(
			'class' => ' d-none')) }}
        
    
    
    
    @endif

@elseif( $product_detail == 'low_stock')
    @if($product_desc == 0)
        @if($product_data['price_per_kg']  == 0)
          {{ Form::number($a.'|'.'low_stock',$product_desc,
                                                                            array('class' => ' d-none'
                                                                             )) }}
        
        @else
        {{ Form::number($a.'|'.'low_stock',$product_desc,
                                                                            array(
                                                                            'class' => ' d-none',
                                                                             )) }}
        
        @endif
    
    
    @else
        {{ Form::number($a.'|'.'low_stock',$product_desc,
                                                                        array(
                                                                        'class' => ' d-none',
                                                                          )) }}
    @endif
    {{--EXTRA STOCK FOR REMOVING OR ADDING STOCK--}}
   
        {{ Form::number($a.'|'.'extra_stock', '',
		array('class' => 'd-none',
		 )) }}
    
    {{--END OFEXTRA STOCK FOR REMOVING OR ADDING STOCK--}}
@elseif($product_detail == 'unset')
    
   
        {{ Form::number($a.'|'.$product_detail, $product_desc,
		array( 'class' => 'd-none'
		)) }}
  


@elseif($product_detail == 'box_size')
    {{ Form::text($a.'|'.$product_detail,$product_desc,
                                                                      array(
                                                                        'class' =>    'd-none',
                                                                       )) }}
@endif
