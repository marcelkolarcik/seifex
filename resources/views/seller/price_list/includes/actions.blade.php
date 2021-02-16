
<div class="row">
{{--PRICE LIST ACTIONS--}}
    <!-- List group -->
    <div class="list-group list-group-horizontal-sm col-md-12 mb-1 " id="myList" role="tablist">
        @if(isset($info->match) && $info->match != false)
           
            @if (Auth::guard('seller')->user()->can('de_activate_buyer', App\ProductList::class))
                @include('seller.default_prices.includes.toggle_activation')
            @endif
           
           @if (Auth::guard('seller')->user()->can('price_product_list', App\ProductList::class))
            <a class="list-group-item list-group-item-action  pt-1 pb-1 mr-1 btn btn-light_green" href="{{ url('/apply_default_prices') }}" role="tab" >
                {{__('Apply your default prices')}}
            </a>
           @endif
       
       @endif
       
       <a class="list-group-item list-group-item-action pt-1 pb-1 btn btn-light_green  mr-1" href="" data-toggle="modal" data-target="#seller_prices" role="tab" >
                {{__('My Prices')}}
       </a>
        <a class="list-group-item   disabled pt-1 pb-1  mr-1 bg-secondary text-light"
           >
            
            {{ $info->matches['currency']}}
            
            {!! Form::text('currency', $info->matches['currency'],[
						   'readonly',
						   'id' => 'currencies',
						   'class' =>  'd-none',
						   'required' => 'required',
						]) !!}
        
        </a>
            @if($info->matches['language'] != '')
        <a class="list-group-item  disabled pt-1 pb-1 bg-secondary text-light"
              >
           
                {{\App\Services\Language::get_language_names([$info->matches['language']],'short_long')['long']}}
            
                {!! Form::text('language',  $info->matches['language'],[
				  'id' => 'languages',
				   'readonly',
				  'class' => 'd-none',
				  'required' => 'required',
				  ]
				 ) !!}
    
         </a>
                @endif
    
    </div>
    
    
    @include('seller.default_prices.includes.modal_default_prices')
    
    <div class="mt-2 mb-2"  id="no_match_language">
        @if(  $info->matches['language']  == '')
            @component('components.main_header_red')
                {{__('You don\'t have prices in buyer language.Create price list in buyers language or contact buyer to create product list in your language or let it go.')}}
            @endcomponent
        @endif
    </div>
    <div class="mb-2" id="no_match_currency">
        @if(  $info->matches['currency']  == '')
            @component('components.main_header_red')
                {{__('It seems like you do not have price list in any of the buyers currencies.
				Add prices in one of the buyers currency  or contact buyer to pay you in one of  your currencies or let it go.')}}
                @component('components.prices_in',['prices_in'  =>  $info->prices_in])
                
                @endcomponent
            @endcomponent
        @endif
    </div>

</div>
@include('includes.uploadFeedback')



