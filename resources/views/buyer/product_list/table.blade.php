
<table class="table ">
    
    <thead>
    <tr>
        @if($preferred_lang['short'] !== $selected_lang['short'])
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$selected_lang['long']}}</th>
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$selected_lang['long']}}</th>
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$selected_lang['long']}}</th>
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$selected_lang['long']}}</th>
        @elseif(isset($translator_language))
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$translator_language['long']}}</th>
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$translator_language['long']}}</th>
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$translator_language['long']}}</th>
            <th scope="col">#</th>
            <th scope="col">{{$preferred_lang['long']}}</th>
            <th scope="col">{{$translator_language['long']}}</th>
        @else
            <th scope="col">#</th>
            <th scope="col">{{__('product')}}</th>
            
            <th scope="col">#</th>
            <th scope="col">{{__('product')}}</th>
            
            <th scope="col">#</th>
            <th scope="col">{{__('product')}}</th>
            
            <th scope="col">#</th>
            <th scope="col">{{__('product')}}</th>
        
        
        
        @endif
    </tr>
    </thead>
    <tbody>
    <tr>
   @if(!$preferred_changed)
       @foreach($product_list as $language  =>$list)
                @foreach($list as $key  => $product)
                    @if(($loop->iteration % 4) - 1 == 0)
                        <tr>
                    @endif
                            <th scope="row">{{$loop->iteration}}</th>
                            <td>
                                {{ Form::text("product_list[".$preferred_lang['short']."][]",
                                        is_numeric($key) ? $product : $key,
                                          [
                                           $preferred_lang['short'] === $selected_lang['short']  ?
                                              '' :
                                              'readonly',
                                          'class' => 'form-control form-control-sm',
                                           ]  ) }}
                            </td>
                            {{--TRANSLATED LANGUAGE PRODUCT--}}
                            @if($preferred_lang['short'] !== $selected_lang['short'])
                                @if(isset($product_list[$selected_lang['short']]))
                                <td >
                                    {{ Form::text("product_list[".$selected_lang['short']."][]",
									$product,
									 [
									 'class' =>  $product ==  '' ?
									 'bg-warning form-control form-control-sm ' :
									 ' form-control form-control-sm bg-light_green',
									  !isset($readonly)?:$readonly,
									 'placeholder'=>$product_list[$language][$key] ]  ) }}
                                </td>
                                 @else
                                    <td >
                                        {{ Form::text("product_list[".$selected_lang['short']."][]",
										'',
										 [
										 'class' =>  'bg-warning form-control form-control-sm ',
										 'placeholder'=>$product_list[$language][$key] ]  ) }}
                                    </td>
                                 @endif
                            @endif
                    @if($loop->iteration % 4 == 0)
                        </tr>
                    @endif
                @endforeach
       @endforeach
       
   @else
        @foreach($product_list as $language  =>$list)
            @foreach($list as $key  => $product)
                @if(($loop->iteration % 4) - 1 == 0)
                    <tr>
                        @endif
                        <th scope="row">{{$loop->iteration}}</th>
                        <td>
                            {{ Form::text("product_list[".$preferred_lang['short']."][]",
									 is_numeric($key) ? null : $key,
									 
									 [ 'class' => 'form-control form-control-sm'])
										
									 
									      }}
                        </td>
                        <td >
                            {{ Form::text("product_list_translator[".$translator_language['short']."][]",
									$product,
									 [
									 'class' =>  'bg-warning form-control form-control-sm ',
									 'readonly',
									 'placeholder'=>$product]  ) }}
                         </td>
                           
                      
                        @if($loop->iteration % 4 == 0)
                    </tr>
    @endif
    @endforeach
    @endforeach
   @endif
    
    {{----}}
    {{--@if(isset($product_list[$preferred_lang['short']]))--}}
    {{--@foreach($product_list[$preferred_lang['short']] as $key =>  $product)--}}
        {{--@if(($loop->iteration % 4) - 1 == 0)--}}
            {{--<tr>--}}
        {{--@endif--}}
                {{--<th scope="row">{{$key + 1}}</th>--}}
                {{--<td>--}}
                    {{--PREDFERRED LANGUAGE PRODUCT --}}
                    {{--{{ Form::text("product_list[".$preferred_lang['short']."][]",--}}
					  {{--$product_list[$preferred_lang['short']][ $key],--}}
						{{--[--}}
						 {{--$preferred_lang['short'] === $selected_lang['short']  ?--}}
							{{--'' :--}}
							{{--'readonly',--}}
						{{--'class' => 'form-control form-control-sm',--}}
						 {{--]  ) }}--}}
                {{--</td>--}}
                {{----}}
         {{--@if($loop->iteration % 4 == 0)--}}
            {{--</tr>--}}
        {{--@endif--}}
    {{--@endforeach--}}
    {{--@endif--}}
    {{--@if(isset($product_list[$selected_lang['short']]) && $selected_lang['short'] != $preferred_lang['short'])--}}
        {{--@foreach($product_list[$selected_lang['short']] as $key =>  $product)--}}
        {{--@if(($loop->iteration % 4) - 1 == 0)--}}
            {{--<tr>--}}
                {{--@endif--}}
                {{--<th scope="row">{{$key + 1}}</th>--}}
                {{--<td>--}}
                    {{--PREDFERRED LANGUAGE PRODUCT --}}
                    {{--{{ Form::text("product_list[".$preferred_lang['short']."][]",--}}
					  {{--$product_list[$preferred_lang['short']][ $key],--}}
						{{--[--}}
						 {{--$preferred_lang['short'] === $selected_lang['short']  ?--}}
							{{--'' :--}}
							{{--'readonly',--}}
						{{--'class' => 'form-control form-control-sm',--}}
						 {{--]  ) }}--}}
                {{--</td>--}}
            {{----}}
               {{----}}
       {{----}}
            {{--<td >--}}
                {{--TRANSLATED LANGUAGE PRODUCT--}}
                {{--{{ Form::text("product_list[".$selected_lang['short']."][]",--}}
				{{--/* IF WE HAVE TRANSLATION , WE WILL DISPLAY IT */--}}
				{{--isset($product_list[$selected_lang['short']][!isset($product_key)? $product : $$product_key])  ?--}}
				 {{--$product_list[$selected_lang['short']][!isset($product_key)? $product : $$product_key] :--}}
				 {{--'',--}}
				 {{--[--}}
				 {{--'class' =>  isset($product_list[$selected_lang['short']][!isset($product_key)? $product : $$product_key]) &&--}}
				  {{--$product_list[$selected_lang['short']][!isset($product_key)? $product : $$product_key] != '' ?--}}
				 {{--'bg-light_green form-control form-control-sm ' :--}}
				 {{--' form-control form-control-sm bg-orange',--}}
				  {{--!isset($readonly)?:$readonly,--}}
				 {{--'placeholder'=>$product_list[$preferred_lang['short']][$key] ]  ) }}--}}
            {{--</td>--}}
         {{--@if($loop->iteration % 4 == 0)--}}
            {{--</tr>--}}
        {{--@endif--}}
        {{--@endforeach--}}
    {{--@endif--}}
    {{----}}
    {{--@if(!isset($product_list[$preferred_lang['short']]))--}}
        {{--@foreach($product_list as $language => $prod_list)--}}
            {{--@if($loop->first)--}}
                {{--@if($preferred_lang['short'] == $selected_lang['short'])--}}
                {{--<div class="bg-warning text-secondary">--}}
                    {{--{{__('Translation in orange is in :language, translate to :preferred language!',['language'=>$language,'preferred'=>$preferred_lang['short']])}}--}}
                {{--</div>--}}
                {{--@endif--}}
            {{--@endif--}}
        {{--@foreach($prod_list as $key =>  $product)--}}
            {{--@if(($loop->iteration % 4) - 1 == 0)--}}
                {{--<tr>--}}
                    {{--@endif--}}
                    {{--<th scope="row">{{$key + 1}}</th>--}}
                  {{----}}
                      {{--@if($preferred_lang['short'] == $selected_lang['short'])--}}
                        {{--<td>--}}
                            {{--PREDFERRED LANGUAGE PRODUCT --}}
                        {{--{{ Form::text("product_list[".$selected_lang['short']."][]",--}}
						 {{--'',--}}
							{{--[--}}
							{{----}}
							{{--'class' => 'form-control form-control-sm',--}}
							 {{--]  ) }}--}}
                        {{--</td>--}}
                      {{----}}
                   {{----}}
                    {{--IF PREFERRED === SELECTED WE WILL DISPLAY ONLY PREFERRED--}}
                   {{----}}
                        {{--<td >--}}
                            {{--TRANSLATED LANGUAGE PRODUCT--}}
                            {{--{{ Form::text("product_list[".$selected_lang['short']."][]",--}}
							{{--/* IF WE HAVE TRANSLATION , WE WILL DISPLAY IT */--}}
							{{--isset($product_list[$language][!isset($product_key)? $product : $$product_key])  ?--}}
							 {{--$product_list[$language][!isset($product_key)? $product : $$product_key] :--}}
							 {{--'',--}}
							 {{--[--}}
							 {{--'class' =>  isset($product_list[$language][!isset($product_key)? $product : $$product_key]) &&--}}
							  {{--$product_list[$language][!isset($product_key)? $product : $$product_key] != '' ?--}}
							 {{--'bg-light_green form-control form-control-sm ' :--}}
							 {{--' form-control form-control-sm bg-orange',--}}
							  {{--!isset($readonly)?:$readonly,--}}
							 {{--'placeholder'=>$product_list[$language][$key] ]  ) }}--}}
                        {{--</td>--}}
                    {{--@else--}}
                        {{--<td >--}}
                            {{--TRANSLATED LANGUAGE PRODUCT--}}
                            {{--{{ Form::text("product_list[".$selected_lang['short']."][]",--}}
							{{--/* IF WE HAVE TRANSLATION , WE WILL DISPLAY IT */--}}
							{{--isset($product_list[$language][!isset($product_key)? $product : $$product_key])  ?--}}
							 {{--$product_list[$language][!isset($product_key)? $product : $$product_key] :--}}
							 {{--'',--}}
							 {{--[--}}
							 {{--'class' =>  isset($product_list[$language][!isset($product_key)? $product : $$product_key]) &&--}}
							  {{--$product_list[$language][!isset($product_key)? $product : $$product_key] != '' ?--}}
							 {{--'bg-light_green form-control form-control-sm ' :--}}
							 {{--' form-control form-control-sm bg-orange',--}}
							  {{--!isset($readonly)?:$readonly,--}}
							 {{--'placeholder'=>$product_list[$language][$key] ]  ) }}--}}
                        {{--</td>--}}
                    {{--@endif--}}
                    {{--@if($loop->iteration % 4 == 0)--}}
                {{--</tr>--}}
    {{--@endif--}}
    {{--@endforeach--}}
    {{--@endforeach--}}
    {{--@endif--}}
</table>
