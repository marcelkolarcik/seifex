@if($translation['needed'])
    <td  style="width: 17%" >
        
        {{ Form::text('translations['.$a.'|'.$product_detail.']', null,
																	array(
																	'class' => 'form-control form-control-sm bg-light_green',
																	 $product_desc    ==  '' ?   'readonly' :'',
																	'placeholder'   =>  __('translation'))) }}
        <br>
        {{ Form::text($a.'|'.$product_detail, $product_desc,
			   array( 'class' => 'form-control form-control-sm ','readonly'    =>  'readonly')) }} </td>
@else
    <td  style="width: 17%">
        @if( isset($translations[$product_name][$td_name]))
            {{ Form::text('translations['.$a.'|'.$product_detail.']',
		   isset($translations[$product_name][$td_name])   ? $translations[$product_name][$td_name] :  '',
						array( 'class' =>   isset($translations[$product_name][$td_name]) ?
							'form-control form-control-sm bg-light_green' :'form-control form-control-sm ',
							'placeholder'=>__('translation'))) }}
            <br>
            
            {{ Form::text($a.'|'.$product_detail, $product_desc,
				  array( 'class' => 'form-control form-control-sm ','readonly'    =>  'readonly')) }}
        @else
            {{ Form::text($a.'|'.$product_detail, $product_desc,
			 array( 'class' => !$is_preferred_language ?' bg-light_green form-control form-control-sm' : 'form-control form-control-sm',
			 'required',
			 $product_desc    ==  '' && !$is_preferred_language  ?   'readonly' :'')) }}
        @endif
    </td>
@endif
