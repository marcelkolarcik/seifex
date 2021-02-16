<div class="row">
    <div class="col-md-12 mb-1">
        <!-- List group -->
        <div class="list-group list-group-horizontal-sm mb-1 " id="myList" role="tablist">
            <a class="list-group-item list-group-item-action active pt-1 pb-1 " data-toggle="list" href="#prices">   {{__('Prices')}}</a>
            <a class="list-group-item list-group-item-action pt-1 pb-0 " data-toggle="list" href="#info" role="tab">Info</a>
            <a class="list-group-item list-group-item-action pt-1 pb-0 " data-toggle="list" href="#messages" role="tab">Messages</a>
            <a class="list-group-item list-group-item-action disabled pt-1 pb-0
             {{!isset($department) ?: 'bg-grey-300'   }}"
               data-toggle="list" href="#department" role="tab">
                {{!isset($department) ?__('Department'): __(str_replace('_',' ',$department))   }}</a>
        </div>
    
        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="prices" role="tabpanel">
                @if (!empty($sorted_extended))
                        @if($adding_new_products && $c_preferred_language != $language || $adding_new_products && $c_preferred_currency != $currency)
                            <form  action="{{ URL::to('update_extended_price_list') }}"  method="post" enctype="multipart/form-data">
                                @csrf
                                @elseif(!$is_preferred_language)
                                    <form  action="{{ URL::to('update_translations') }}"  method="post" enctype="multipart/form-data">
                                        @csrf
                            
                                        @else
                                            <form  action="{{ URL::to('update_extended_price_list') }}"  method="post" enctype="multipart/form-data">
                                                @csrf
                                                @endif
                                    
                                                <div class="card ">
                                                    <div class="card-header bg-light_green text-dark ">
                                                        {{--DELETE PRICE LIST--}}
                                                        @if (Auth::guard('seller')->user()->can('delete_default_prices', App\DefaultPriceList::class))
                                                            <button id="delete_price_list" class="btn btn-danger btn-sm float-right"
                                                                    department = "{{$department}}"
                                                                    seller_id = "{{ Auth::guard('seller')->user()->id }}"
                                                                    seller_company_id = "{{$seller_company_id}}"
                                                                    title             =   "{{__('Delete price list for ').str_replace('_',' ',$department).' ?'   }}"
                                                                    text             =    "{{__('All your buyers price lists will be deleted too !')}}"
                                                                    wrong              ="{{__('Something went wrong.')}}"
                                                                    later              ="{{__('Please try again later.')}}"
                                                            >{{__('Delete Price list')}}
                                                            </button>
                                                        @endif
                                                        @include('seller.default_prices.includes.header')
                                                    </div>
                                                    <div class="row">
                                                        @if(isset($old_preferred_language))
                                                            <div class="col-md-12 bg-light text-danger">
                                                                @component('components.main_header_red')
                                                                    You have changed your preferred language {{$old_preferred_language }} to new preferred language
                                                                    {{\App\Services\Language::get_language_names([$c_preferred_language],'short_long')['long'] }}.To make it easier, we have included
                                                                    your old preferred language translation.These products are in
                                                                    <span class="bg-warning text-dark btn btn-sm">{{$old_preferred_language }}</span>.
                                                                    You will need to add those products in your preferred language
                                                                    <span  class="bg-light text-dark btn btn-sm">
                                {{\App\Services\Language::get_language_names([$c_preferred_language],'short_long')['long'] }}</span>
                                                            </div>
                                                            @endcomponent
                                                        @endif
                                                        @if($adding_new_products && $c_preferred_language != $language ||  $adding_new_products && $c_preferred_currency != $currency)
                                                
                                                            <div class="col-md-12 bg-light text-danger">
                                                                @if($c_preferred_language != $language  || $c_preferred_currency != $currency)
                                                        
                                                                    @if($c_preferred_language != $language )
                                                                        @component('components.main_header_red')
                                                                            These products are in
                                                                            <span class="bg-warning text-dark btn btn-sm">{{\App\Services\Language::get_language_names([$language],'short_long')['long'] }}</span>.
                                                                            You will need to add those products in your preferred language
                                                                            <span  class="bg-light text-dark btn btn-sm"> {{\App\Services\Language::get_language_names([$c_preferred_language],'short_long')['long'] }}</span>
                                                                            with prices in your preferred currency <span  class="bg-light text-dark btn btn-sm"> {{$c_preferred_currency}}</span>.and
                                                                            information about the products and stock level, before you can use them. Leave it empty if you don't have some products.
                                                                        @endcomponent
                                                                    @elseif($c_preferred_currency != $currency)
                                                                        @component('components.main_header_red')
                                                                           These products are in  <span class="bg-warning text-dark btn btn-sm">{{$currency}} </span>. You will need to add those products in your preferred language
                                                                            <span  class="bg-light text-dark btn btn-sm"> {{\App\Services\Language::get_language_names([$c_preferred_language],'short_long')['long'] }}</span>
                                                                            with prices in your preferred currency <span  class="bg-light text-dark btn btn-sm"> {{$c_preferred_currency}}</span>.and
                                                                            information about the products and stock level, before you can use them. Leave it empty if you don't have some products.
                                                                        @endcomponent
                                                                    @endif
                                                                @endif
                                                                <div id="product_prices" >
                                                                    <table class="table table-sm table-hover table-bordered" id="default_seller_prices">
                                                                        @include('seller.default_prices.includes.table_head_adding_in_forein_language')
                                                                        {{--{{ $is_preferred}} {{ dump(json_encode($translation)) }} {{ dump(json_encode($conversion)) }}--}}
                                                                        <tbody>
                                                            
                                                                        <?php $a = 1;?>
                                                                        {{-- UPDATING DEFAULT PRICE LIST FOR DEPARTMENT --}}
                                                            
                                                                        @foreach($sorted_extended as $product_name => $product_data)
                                                                            <tr class= {{ in_array($product_data['product_name'], $new_products_extended) ? 'bg-light_green' : ''}}   >
                                                                   {{-- {{dd($product_data['product_name'])}}--}}
                                                                                @foreach($product_data as $product_detail => $product_desc)
                                                                        
                                                                                    {{--TO TRANSLATE--}}
                                                                                    @if($product_detail == 'product_name')
                                                                                        @include('seller.default_prices.includes.to_translate',['td_name'=>'product_name'])
                                                                                    @elseif( $product_detail == 'type_brand')
                                                                                        @include('seller.default_prices.includes.to_translate',['td_name'=>'type_brand'])
                                                                                    @elseif($product_detail == 'additional_info')
                                                                                        @include('seller.default_prices.includes.to_translate',['td_name'=>'additional_info'])
                                                                                    @endif
                                                                                    {{--END OF TO TRANSLATE--}}
                                                                        
                                                                                    {{--SHOW PRICES--}}
                                                                        
                                                                                    {{--TO CONVERT PRICES--}}
                                                                                    @if($product_detail == 'price_per_kg')
                                                                                        <td  style="width: 8%" >
                                                                                            {{ Form::text('additional_products['.$a.'|'.$product_detail.']', $product_desc,
																							array(
																							'required',
																							'class' =>  $product_desc == '' ? 'form-control form-control-sm pl-1 pr-1    bg-warning  ':  'form-control form-control-sm pl-1 pr-1',
																							'placeholder'   =>  __('required')
																							)) }}
                                                                                        </td>
                                                                                    @elseif($product_detail == 'box_price')
                                                                                        <td  style="width: 10%">{{ Form::text('additional_products['.$a.'|'.$product_detail.']',$product_desc,
                                                                                array(
                                                                                'class' =>  'form-control form-control-sm pl-1 pr-1   ',
                                                                               )) }}
                                                                                        </td>
                                                                                    @endif
                                                                        
                                                                                    {{--END OF TO CONVERT PRICES--}}
                                                                        
                                                                                    {{--SHOW UNCHANGABLE DATA IF DIFFERENT LANGUAGE OR CURRENCY---}}
                                                                        
                                                                                    {{--PRODUCT DATA UNCHANGABLE IF DIFFERENT LANGUAGE OR CURRENCY--}}
                                                                                    @if($product_detail == 'product_code')
                                                                                        <td  style="width: 8%" >
                                                                                
                                                                                            {{ Form::text('additional_products['.$a.'|'.$product_detail.']', $product_desc,
																							array( 'class' => 'form-control form-control-sm pl-1 pr-1   '  )) }}
                                                                            
                                                                                        </td>
                                                                        
                                                                                    @elseif($product_detail == 'stock_level')
                                                                                        <td  style="width: 8%" >
                                                                                
                                                                                            {{ Form::number('additional_products['.$a.'|'.$product_detail.']', $product_desc,
																							array(
																							
																							'required',
																							'class' =>  $product_desc == '' ? 'form-control form-control-sm pl-1 pr-1    bg-warning  ':  'form-control form-control-sm pl-1 pr-1',
																							'placeholder'   =>  __('required')
																							 )) }}
                                                                            
                                                                                        </td>
                                                                        
                                                                                    @elseif( $product_detail == 'low_stock')
                                                                                        <td  style="width: 8%">{{ Form::number('additional_products['.$a.'|'.$product_detail.']',$product_desc,
                                                                                array(/*'min' =>'1',*/
                                                                                            'required',
																							'class' =>  $product_desc == '' ? 'form-control form-control-sm pl-1 pr-1    bg-warning  ':  'form-control form-control-sm pl-1 pr-1',
																							'placeholder'   =>  __('required')
                                                                                ))
                                                                                 }}</td>
                                                                                        {{--EXTRA STOCK FOR REMOVING OR ADDING STOCK--}}
                                                                                        <td  style="width: 8%">
                                                                                            {{ Form::number('additional_products['.$a.'|'.'extra_stock'.']', '',
																							array(
																							'class' => 'form-control form-control-sm pl-1 pr-1   '
																							)) }}
                                                                                        </td>
                                                                                        {{--END OFEXTRA STOCK FOR REMOVING OR ADDING STOCK--}}
                                                                                    @elseif($product_detail == 'unset')
                                                                            
                                                                                        <td  style="width: 8%">
                                                                                            {{ Form::number('additional_products['.$a.'|'.$product_detail.']', $product_desc,
																							array( 'class' => $product_desc > 0 ? 'form-control form-control-sm pl-1 pr-1   bg-danger ': '  '.'form-control form-control-sm pl-1 pr-1   ',
																							)) }}
                                                                                        </td>
                                                                        
                                                                        
                                                                                    @elseif($product_detail == 'box_size')
                                                                                        <td  style="width: 10%">{{ Form::text('additional_products['.$a.'|'.$product_detail.']',$product_desc,
                                                                                array(
                                                                                'class' =>    'form-control form-control-sm pl-1 pr-1   '
                                                                               )) }}</td>
                                                                                    @endif
                                                                                    {{--END OF PRODUCT DATA UNCHANGABLE IF DIFFERENT LANGUAGE OR CURRENCY--}}
                                                                    
                                                                                @endforeach
                                                                
                                                                            </tr>
                                                                        </tbody>
                                                                        <?php $a ++ ;?>
                                                                        @endforeach
                                                        
                                                        
                                                                    </table>
                                                                    <div id="update_prices">
                                                                        {!! Form::submit(__('Save your changes'), ['name' => 'submitbutton' , 'class' => 'btn btn-danger form-control form-control-sm pl-1 pr-1   ']) !!}
                                                                    </div>
                                                                </div>
                                                
                                                            </div>
                                            
                                            
                                            
                                                        @else
                                                            {{--CONVERSION DIV--}}
                                                            <div id="conversion" class="col-md-6">
                                                                @if(!$is_preferred_currency)
                                                                    @if($conversion['needed'])
                                                            
                                                                        <div class="  align-items-center col img-thumbnail ">
                                                                            {{__('This is prices in your currency : ')}} {{$conversion['from']}}
                                                                            {{__('because you do not have prices in ')}} {{ $conversion['to'] }} <br>
                                                                        </div>
                                                                    @endif
                                                                    @if(session()->has('error'))
                                                                        {{$error['error']}}
                                                                    @endif
                                                        
                                                        
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text bg-secondary text-light">1 {{$conversion['from']}} =</div>
                                                                        </div>
                                                                        <input name="converter" type="text" class="form-control" id="rate"
                                                            
                                                                               placeholder="{{$rate}}">
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text bg-secondary text-light">{{$conversion['to']}}</div>
                                                                        </div>
                                                        
                                                                    </div>
                                                                    <div>
                                                                        @if($saved_rate != 0)
                                                                            <span class="bg-warning">   converted with saved rate : {{$saved_rate}}</span>
                                                                        @else
                                                                            <span class="bg-light_green">  converted with rate : {{$rate}}</span>
                                                                        @endif
                                                                    </div>
                                                                    {{ Form::button('convert '.$conversion['from'].' => '.$conversion['to'],
																										 [ 'class' => 'form-control col bg-orange text-light',
																										 'id'    =>  'converter',
																										 'data-department'=>$department,
																										 'data-seller_company_id'=>$seller_company_id,
																										 'data-preferred_currency'=>$conversion['from_short'],
																										 'data-currency'=>$conversion['to_short']]) }}
                                                    
                                                                @endif
                                                            </div>
                                                            {{--TRANSLATION DIV--}}
                                                            <div id="translation" class="col-md-6">
                                                                @if(!$is_preferred_language)
                                                                    @if($translation['needed'])
                                                                        <div class="  align-items-center col img-thumbnail bg-orange text-light">
                                                                            {{__('This is products in : ')}} <span class="text-secondary btn btn-sm bg-grey-300">{{$translation['from']}}</span>
                                                                            {{__('because you do not have names in ')}}<span class="text-secondary btn btn-sm bg-warning"> {{ $translation['to'] }} </span>
                                                                        </div>
                                                                    @else
                                                                        <div class="  align-items-center col img-thumbnail bg-orange text-light">
                                                                            {{__('Keep translating from : ')}} <span class="text-secondary btn btn-sm bg-grey-300">{{$translation['from']}}</span>
                                                                            {{__('to ')}}<span class="text-secondary btn btn-sm bg-warning"> {{ $translation['to'] }} </span><br>
                                                                            {{__('Remember to save it !')}}
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                    
                                                @if($adding_new_products && $c_preferred_language == $language  && $c_preferred_currency == $currency || ! $adding_new_products )
                                        
                                                    {{--<div class="accordion " id="accordionBuyers">--}}
                                                    {{--<div class="card" >--}}
                                                    {{--<button class="btn btn-sm   btn-secondary" type="button" data-toggle="collapse" data-target="#buyers" aria-expanded="true" aria-controls="buyers"  id="headingOne">--}}
                                                    {{--{{__('Buyers')}} <i class="fas fa-caret-down"></i>--}}
                                                    {{--</button>--}}
                                                    {{----}}
                                                    {{--<div id="buyers" class="collapse" aria-labelledby="headingOne" data-parent="#accordionBuyers">--}}
                                                    {{--<div class="card-body">--}}
                                                    {{--<ul class="list-group">--}}
                                                    {{--@foreach($buyers as $buyer)--}}
                                                    {{--<li class="list-group-item text-dark">--}}
                                                    {{--{{ $buyer->buyer_company_name  }}    {{ $buyer->buyer_company_id  }}--}}
                                                    {{--</li>--}}
                                                    {{--@endforeach--}}
                                                    {{--</ul>--}}
                                                    {{--</div>--}}
                                                    {{--</div>--}}
                                                    {{--</div>--}}
                                                    {{--</div>--}}
                                        
                                        
                                                    <div id="product_prices" >
                                            
                                                        <table class="table table-sm table-hover table-bordered" id="default_seller_prices">
                                                            @include('seller.default_prices.includes.table_head')
                                                            {{--{{ $is_preferred}} {{ dump(json_encode($translation)) }} {{ dump(json_encode($conversion)) }}--}}
                                                            <tbody>
                                                
                                                            <?php $a = 1;?>
                                                            {{-- UPDATING DEFAULT PRICE LIST FOR DEPARTMENT --}}
                                                
                                                            @foreach($sorted_extended as $product_name => $product_data)
                                                                @if($product_data['stock_level'] == '')
                                                                    <tr class='bg-light_green'>
                                                                    @if($loop->first)
                                                                        @component('components.main_header_red')
                                                                            {{__('If you have added new prices for product, add stock level and low stock warning')}}
                                                                        @endcomponent
                                                                    @endif
                                                                @else
                                                                    <tr>
                                                                @endif
                                                                <tr class={{$product_data['stock_level'] == '' ? 'bg-light_green' : ''}}>
                                                        
                                                                    @foreach($product_data as $product_detail => $product_desc)
                                                            
                                                                        {{--TO TRANSLATE--}}
                                                                        @if($product_detail == 'product_name')
                                                                            @include('seller.default_prices.includes.to_translate',['td_name'=>'product_name'])
                                                                        @elseif( $product_detail == 'type_brand')
                                                                            @include('seller.default_prices.includes.to_translate',['td_name'=>'type_brand'])
                                                                        @elseif($product_detail == 'additional_info')
                                                                            @include('seller.default_prices.includes.to_translate',['td_name'=>'additional_info'])
                                                                        @endif
                                                                        {{--END OF TO TRANSLATE--}}
                                                            
                                                                        {{--SHOW PRICES--}}
                                                                        @if($is_preferred_language || !$is_preferred_currency)
                                                                            {{--TO CONVERT PRICES--}}
                                                                            @if($product_detail == 'price_per_kg')
                                                                                <td  style="width: 8%" >
                                                                                    {{ Form::text($a.'|'.$product_detail, $product_desc,
																					array('required'=>'required',
																					'class' => 'form-control form-control-sm pl-1 pr-1   ',
																					$is_preferred==true ?  :'readonly'
																					)) }}
                                                                                </td>
                                                                            @elseif($product_detail == 'box_price')
                                                                                <td  style="width: 10%">{{ Form::text($a.'|'.$product_detail,$product_desc,
                                                                                array(
                                                                                'class' =>    'form-control form-control-sm pl-1 pr-1   ',
                                                                               $is_preferred==true ?  :'readonly' )) }}
                                                                                </td>
                                                                            @endif
                                                                        @endif
                                                                        {{--END OF TO CONVERT PRICES--}}
                                                            
                                                                        {{--SHOW UNCHANGABLE DATA IF DIFFERENT LANGUAGE OR CURRENCY---}}
                                                                        @if($is_preferred_language && $is_preferred_currency)
                                                                            {{--PRODUCT DATA UNCHANGABLE IF DIFFERENT LANGUAGE OR CURRENCY--}}
                                                                            @if($product_detail == 'product_code')
                                                                                <td  style="width: 8%" >
                                                                        
                                                                                    {{ Form::text($a.'|'.$product_detail, $product_desc,
																					array( 'class' => 'form-control form-control-sm pl-1 pr-1   ',
																					 $is_preferred==true ?  :'readonly' )) }}
                                                                    
                                                                                </td>
                                                                
                                                                            @elseif($product_detail == 'stock_level')
                                                                                @if($product_data['stock_level'] == "")
                                                                                    <td  style="width: 8%" >
                                                                                        {{-- SELLER DOESN'T HAVE PRICE FOR THE PRODUCT--}}
                                                                                        @if($product_data['price_per_kg']  == 0)
                                                                                            {{ Form::number($a.'|'.$product_detail, null,
																							array(/*'min' =>'1',*/ 'class' => ' form-control form-control-sm pl-1 pr-1   ')) }}
                                                                                        @else
                                                                                            {{ Form::number($a.'|'.$product_detail, null,
																							array(/*'min' =>'1',*/'required'=>'required',
																							'class' => ' form-control form-control-sm pl-1 pr-1   bg-warning',
																							'placeholder'=>'required')) }}
                                                                                        @endif
                                                                                    </td>
                                                                    
                                                                    
                                                                                @elseif($product_data['low_stock'] >= $product_data['stock_level'])
                                                                                    <td  style="width: 8%" >
                                                                            
                                                                                        {{ Form::number($a.'|'.$product_detail, $product_desc,
																						array('title'=>'low stock warning',
																						'readonly' => 'readonly',
																						'class' => 'bg-danger text-light form-control form-control-sm pl-1 pr-1   ')) }}
                                                                        
                                                                                    </td>
                                                                                @else
                                                                                    <td  style="width: 8%" >
                                                                            
                                                                                        {{ Form::number($a.'|'.$product_detail, $product_desc,
																						array('required'=>'required',
																						'readonly' => 'readonly',
																						'class' => 'form-control form-control-sm pl-1 pr-1   ')) }}
                                                                        
                                                                                    </td>
                                                                    
                                                                    
                                                                                @endif
                                                                
                                                                            @elseif( $product_detail == 'low_stock')
                                                                                @if($product_desc == 0)
                                                                                    @if($product_data['price_per_kg']  == 0)
                                                                                        <td  style="width: 8%">{{ Form::number($a.'|'.'low_stock',$product_desc,
                                                                                    array(/*'min' =>'1',*/'class' => 'form-control form-control-sm pl-1 pr-1   ',
                                                                                     $is_preferred==true ?  :'readonly' )) }}</td>
                                                                        
                                                                                    @else
                                                                                        <td  style="width: 8%">{{ Form::number($a.'|'.'low_stock',$product_desc,
                                                                                    array(/*'min' =>'1',*/'required'=>'required',
                                                                                    'class' => 'form-control form-control-sm pl-1 pr-1   bg-warning',
                                                                                    'placeholder'=>'required',
                                                                                     $is_preferred==true ?  :'readonly' )) }}</td>
                                                                        
                                                                                    @endif
                                                                    
                                                                    
                                                                                @else
                                                                                    <td  style="width: 8%">{{ Form::number($a.'|'.'low_stock',$product_desc,
                                                                                array(/*'min' =>'1',*/'required'=>'required',
                                                                                'class' => 'form-control form-control-sm pl-1 pr-1  ',
                                                                                 $is_preferred==true ?  :'readonly' )) }}</td>
                                                                                @endif
                                                                                {{--EXTRA STOCK FOR REMOVING OR ADDING STOCK--}}
                                                                                <td  style="width: 8%">
                                                                                    {{ Form::number($a.'|'.'extra_stock', '',
																					array('class' => 'form-control form-control-sm pl-1 pr-1   ',
																					 $is_preferred==true ?  :'readonly' )) }}
                                                                                </td>
                                                                                {{--END OFEXTRA STOCK FOR REMOVING OR ADDING STOCK--}}
                                                                            @elseif($product_detail == 'unset')
                                                                    
                                                                                <td  style="width: 8%">
                                                                                    {{ Form::number($a.'|'.$product_detail, $product_desc,
																					array( 'class' => $product_desc > 0 ? 'form-control form-control-sm pl-1 pr-1   bg-danger text-light pl-1 pr-1': '  '.'form-control form-control-sm pl-1 pr-1   ',
																					'readonly' => 'readonly')) }}
                                                                                </td>
                                                                
                                                                
                                                                            @elseif($product_detail == 'box_size')
                                                                                <td  style="width: 10%">{{ Form::text($a.'|'.$product_detail,$product_desc,
                                                                                array(
                                                                                'class' =>    'form-control form-control-sm pl-1 pr-1   ',
                                                                               $is_preferred==true ?  :'readonly' )) }}</td>
                                                                            @endif
                                                                
                                                                            {{--END OF PRODUCT DATA UNCHANGABLE IF DIFFERENT LANGUAGE OR CURRENCY--}}
                                                                        @endif
                                                                        @if(!$is_preferred_language)
                                                                            <div class="col-md-1">
                                                                                @include('seller.default_prices.includes.hidden_data')
                                                                            </div>
                                                            
                                                                        @endif
                                                                    @endforeach
                                                                    <td>
                                                                        @if($is_preferred)
                                                                            {{--DELETE PRODUCT--}}
                                                                            @if (Auth::guard('seller')->user()->can('delete_default_prices', App\DefaultPriceList::class))
                                                                    
                                                                                <button type="button" name="{{$product_name}}"
                                                                                        department = "{{ isset($department) ? $department : '' }}"
                                                                                        seller_company_id = "{{$seller_company_id}}"
                                                                                        class="btn btn-sm btn-outline-danger delete_product"
                                                                                        delete_string   =   "{{__('Delete')}}"
                                                                                        wrong              ="{{__('Something went wrong.')}}"
                                                                                        later              ="{{__('Please try again later.')}}">
                                                                        
                                                                                    {{__('x')}}
                                                                                </button>
                                                                            @endif
                                                                            {{--END OF DELETE PRODUCT--}}
                                                                        @endif
                                                                    </td>
                                                    
                                                                </tr>
                                                
                                                            </tbody>
                                                            <?php $a ++ ;?>
                                                            @endforeach
                                            
                                            
                                                        </table>
                                                        @if (Auth::guard('seller')->user()->can('edit_default_prices', App\DefaultPriceList::class))
                                                            @if($is_preferred)
                                                                @include('seller.default_prices.includes.add_new_product_btn')
                                                            @endif
                                                        @endif
                                                    </div>
                                        
                                                    <hr/>
                                                    @if (Auth::guard('seller')->user()->can('edit_default_prices', App\DefaultPriceList::class))
                                                        @if($is_preferred_currency && $is_preferred_language)
                                                
                                                            <div id="update_prices" >
                                                                {!! Form::submit(__('Save your changes'), ['name' => 'submitbutton' , 'class' => ' btn btn-danger form-control form-control-sm pl-1 pr-1   ']) !!}
                                                            </div>
                                                        @endif
                                                        @if(!$is_preferred_language)
                                                            <div id="update_translation">
                                                                {!! Form::submit(__('translate '.$translation['from'].' => '.$translation['to']), ['name' => 'submitbutton' , 'class' => 'btn btn-danger form-control form-control-sm pl-1 pr-1   ']) !!}
                                                            </div>
                                                        @endif
                                                    @endif
                                            </form> </form> </form>
            
                        @endif
        
                  
                    {{-- NEW DEFAULT PRICE LIST FOR DEPARTMENT --}}
                
                @else
                   
                        <form  action="{{ URL::to('save_extended_price_list') }}"  method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-header bg-secondary text-light ">
                                @include('seller.default_prices.includes.header')
                            </div>
                            @if(isset($department))
                               
                                    <div id="product_prices"  >
                                        <table class="table table-sm table-responsive" id="default_seller_prices">
                                            @include('seller.default_prices.includes.table_head')
                                            <tr>
                                                <td style="width: 20%" >{{ Form::text('1|product_name', null,
                                                    array('required'=>'required','class' => 'form-control form-control-sm pl-1 pr-1   bg-warning ','placeholder'=>__('required'))) }}</td>
                                                <td style="width: 10%" >{{ Form::text('1|product_code', null,
                                                    array('class' => 'form-control form-control-sm pl-1 pr-1   ','placeholder'=>__('optional'))) }}</td>
                                                <td style="width: 10%" >{{ Form::text('1|price_per_kg', null,
                                                    array('required'=>'required','class' => 'form-control  form-control-sm pl-1 pr-1   bg-warning','placeholder'=>__('required'))) }}</td>
                                                <td style="width: 10%" >{{ Form::text('1|stock_level', null,
                                                    array('required'=>'required','class' => 'form-control  form-control-sm pl-1 pr-1   bg-warning','placeholder'=>__('required'))) }}</td>
                                    
                                                <td style="width: 5%" >{{ Form::text('1|low_stock', null,
                                                    array('class' => 'form-control  form-control-sm pl-1 pr-1   bg-warning','placeholder'=>__('required'))) }}</td>
                                                <td style="width: 10%" >{{ Form::text('1|extra_stock', null,
                                                    array('class' => 'form-control  form-control-sm pl-1 pr-1   ','placeholder'=>__('optional'))) }}</td>
                                                <td style="width: 20%">{{ Form::text('1|type_brand', null,
                                                    array('class' => 'form-control form-control-sm pl-1 pr-1   ','placeholder'=>__('optional'))) }}</td>
                                                <td style="width: 10%">{{ Form::text('1|box_size', null,
                                                    array('class' => 'form-control form-control-sm pl-1 pr-1   ','placeholder'=>__('optional'))) }}</td>
                                                <td style="width: 10%">{{ Form::text('1|box_price', null,
                                                    array('class' => 'form-control form-control-sm pl-1 pr-1   ','placeholder'=>__('optional'))) }}</td>
                                                <td style="width: 10%">{{ Form::text('1|additional_info', null,
                                                    array('class' => 'form-control form-control-sm pl-1 pr-1   ','placeholder'=>__('optional'))) }}</td>
                                                <td style="width: 5%">{{ Form::text('1|unset', null,
                                                    array('class' => 'form-control form-control-sm pl-1 pr-1   ','readonly' => 'readonly')) }}</td>
                                            </tr>
                                            {{ Form::text('1|old_hash_name',null,
																									  array(
																									  'class' =>    'd-none',
																									'readonly' )) }}
                                        </table>
                                        @if (Auth::guard('seller')->user()->can('edit_default_prices', App\DefaultPriceList::class))
                                            @if($is_preferred)
                                                @include('seller.default_prices.includes.add_new_product_btn')
                                            @endif
                                        @endif
                                    </div>
                                    <hr/>
                                    @if (Auth::guard('seller')->user()->can('edit_default_prices', App\DefaultPriceList::class))
                                        <div id="create_prices" >
                                            {!! Form::submit(__('Create your price list'), ['name' => 'submitbutton' , 'class' => 'btn btn-danger form-control']) !!}
                                        </div>
                                    @endif
                               
                            @endif
                        </form>
              
    
                
                @endif
            </div>
            <div class="tab-pane" id="info" role="tabpanel">
                <ul class="list-group">
                    <li class=" list-group-item ">
                        {{__('Here, you can create your default price list for your default products.')}}
                    </li>
                    <li class=" list-group-item ">
                        {{__('It will be useful, when you will be pricing buyer\'s products.')}}
                    </li>
                    <li class=" list-group-item ">
                        {{__('It will allow you to apply your default prices with one click.')}}
                    </li>
                    <li class=" list-group-item ">
                        {{__('Once you apply your default prices, you have the option to adjust it again.')}}
                    </li>
                    <li class=" list-group-item ">
            
                        {{__('It might take you a while.')}}
        
                    </li>
                    <li class=" list-group-item ">
                        <code>
                            {{__('We will keep building your default prices,')}}
                        </code>
                    </li>
                    <li class=" list-group-item ">
            
                        <p> {{__('With many buyers, you will make time by pricing most of their products with one click.')}}</p>
        
                    </li>
    
                </ul>
            </div>
            <div class="tab-pane" id="messages" role="tabpanel">messages</div>
            <div class="tab-pane" id="department" role="tabpanel">settings</div>
        </div>
        
       
    </div>
</div>
   



