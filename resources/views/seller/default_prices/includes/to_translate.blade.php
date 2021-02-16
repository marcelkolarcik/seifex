@if(isset($old_preferred_language))
    {{ Form::text($a.'|'.'old_hash_name',$product_name,
																				array(
																				'class' =>    'd-none',
																			  'readonly' )) }}
    <td  style="width: 33%" >  {{ Form::text($a.'|'.$product_detail, null,
					   array( 'class' => 'form-control form-control-sm bg-warning pl-1 pr-1  ',
					 'placeholder'   =>  $product_desc,
					 $product_detail != 'product_name' ? :'required')) }}
    </td>
@elseif($adding_new_products && $c_preferred_language != $language ||  $adding_new_products && $c_preferred_currency != $currency)
    <td  style="width: 33%" >
        {{ Form::text('additional_products['.$a.'|'.'old_hash_name'.']' ,null,
																				  array(
																				  'class' =>    'd-none',
																				'readonly' )) }}
       
        {{ Form::text('additional_products['.$a.'|'.$product_detail.']'	, $c_preferred_language != $language ? null: $product_desc,
																	array(
																	'class' => 'form-control form-control-sm pl-1 pr-1  ',
																	'placeholder'   =>  \App\Services\Language::get_language_names([$c_preferred_language],'short_long')['long']
		
																	)) }}
        @if($c_preferred_language != $language)
        <br>
        {{ Form::text('translations['.$a.'|'.$product_detail.']', $product_desc,
			   array( 'class' => 'form-control form-control-sm pl-1 pr-1   bg-warning',
															'placeholder'   =>  \App\Services\Language::get_language_names([$language],'short_long')['long'])) }} </td>
        @endif


@else
    {{ Form::text($a.'|'.'old_hash_name',$product_name,
																				  array(
																				  'class' =>    'd-none',
																				'readonly' )) }}
    @if($translation['needed'])
        <td  style="width: 33%" >
          
            {{ Form::text('translations['.$a.'|'.$product_detail.']', null,
                                                                        array(
                                                                        'class' => 'form-control form-control-sm pl-1 pr-1  bg-warning',
                                                                         $product_desc    ==  '' ?   'readonly' :'',
                                                                        'placeholder'   =>  \App\Services\Language::get_language_names([$language],'short_long')['long'] )) }}
            <br>
            {{ Form::text($a.'|'.$product_detail, $product_desc,
                   array( 'class' => 'form-control form-control-sm pl-1 pr-1  ','readonly'    =>  'readonly')) }} </td>
    @else
        
        <td  style="width: 33%" >
            @if(!$is_preferred_language)
         
            {{ Form::text('translations['.$a.'|'.$product_detail.']',
           isset($translations[$product_name][$td_name] )  && $translations[$product_name][$td_name] != ""     ?
           $translations[$product_name][$td_name] :  '',
                           array( 'class' =>        isset($translations[$product_name][$td_name] )  && $translations[$product_name][$td_name] != ""
                           ? 'form-control form-control-sm pl-1 pr-1' :'form-control form-control-sm pl-1 pr-1  bg-warning',
                              
                               'placeholder'=> \App\Services\Language::get_language_names([$language],'short_long')['long'],
                                $product_desc    ==  '' ?   'readonly' :'',)) }}
            <br>
        
            {{ Form::text($a.'|'.$product_detail, $product_desc,
                  array( 'class' => 'form-control form-control-sm pl-1 pr-1  ','readonly'    =>  'readonly')) }}
             @else
               
                {{ Form::text($a.'|'.$product_detail, $product_desc,
                     array( 'class' => 'form-control form-control-sm pl-1 pr-1  ',
                    $product_detail ==  'product_name' ?   'readonly' :'')) }}
                
              
            @endif
        </td>
    @endif
@endif
