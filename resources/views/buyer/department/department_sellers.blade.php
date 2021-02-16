<div class="list-group list-group-horizontal-sm" >

</div>

<div class="list-group list-group-horizontal-sm  mb-3" >
<a class="list-group-item list-group-item-action pt-1 pb-1 disabled" >{{__('Company name')}}</a>
<a class="list-group-item list-group-item-action pt-1 pb-1 disabled" >{{__('Prices')}}</a>
<a class="list-group-item list-group-item-action pt-1 pb-1 disabled" >{{__('Action')}}</a>
<a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Language')}}</a>
<a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Currency')}}</a>
    <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Delivery days')}}</a>
    <a class="list-group-item list-group-item-action pt-1 pb-1 disabled"  >{{__('Payment frequency')}}</a>
</div>
@if(!isset($seller_status['']))
    @foreach($seller_status as  $sc_id    =>  $seller)
    
       
      
   
        <div class="list-group list-group-horizontal-sm text-primary mb-1"  >
            <a class="list-group-item list-group-item-sm list-group-item-action pt-1 pb-1
{{($seller->activated_by_buyer == 1 && $seller->activated_by_seller == 1) ? ' ': ' bg-warning'}}"
               href="/buyer/about/{{$seller->seller_company_id}}/seller">
                {{$seller->seller_company_name}}
               
            </a>
            <a class="list-group-item list-group-item-action pt-1 pb-1 {{isset($active_seller_id) ? ' bg-light_green ' : '' }}" href=
            "{{ Auth::guard('buyer')->user()->can('see_prices', App\ProductList::class) ?
             '/price_list/'.$department.'/'.$seller->seller_company_id.'/'.$seller->buyer_company_id : ''  }}" >
                
                @if(Auth::guard('buyer')->user()->can('see_prices', App\ProductList::class))
                {{__('Prices') }}
                @endif
            </a>
          
          
                @if(Auth::guard('buyer')->user()->can('de_activate_seller', App\ProductList::class) || \Auth::guard('buyer')->user()->role == 'buyer_owner')
                    @if($seller->activated_by_seller == 0)
                        <small class="bg-warning list-group-item list-group-item-action pt-1 pb-1">{{__('deactivated by seller')}}</small>
                    @else
                        @if( $seller->activated_by_buyer == 1)
                        <button title=" {{__('Deactivate')}}   {{ $seller->seller_company_name }} ?" type="button"

                                          seller_company_id="{{ $seller->seller_company_id }}"
                                          buyer_company_id="{{ $seller->buyer_company_id }}"
                                          department = "{{$seller->department}}"
                                          url = "deactivate_seller"
                                          text="{{__('By clicking Yes!, you agree to Terms and Conditions of Seifex.com !')}}"
                                          wrong       ="{{__('Something went wrong.')}}"
                                          later        ="{{__('Please try again later.')}}"

                                          class="toggle_seller bg-warning list-group-item list-group-item-sm list-group-item-action  pt-1 pb-1" >
                            {{__('Deactivate')}}
                        </button>
                        @else
                        <button  title="{{__('Activate')}} {{ $seller->seller_company_name }} ?"
        
                                 seller_company_id="{{ $seller->seller_company_id }}"
                                 buyer_company_id="{{ $seller->buyer_company_id }}"
                                 department = "{{$seller->department}}"
                        url = "activate_seller"
                        text="{{__('By clicking Yes!, you agree to Terms and Conditions of Seifex.com !')}}"
                        wrong       ="{{__('Something went wrong.')}}"
                        later        ="{{__('Please try again later.')}}"
                        
                        class="toggle_seller  list-group-item list-group-item-action pt-1 pb-1 bg-light_green" >
                        {{__('Activate')}}
                        </button>
                        @endif
                    @endif
                @endif
         
            
            <div class="list-group-item list-group-item-action pt-1 pb-1 disabled">
                {{   \App\Services\Language::get_language_names([$seller->language], 'short_long')['long']         }}&nbsp;
                <small class="bg-warning">{{$seller->buyer_disabled_language == 0 ? '':  __('de-activated')  }}</small>
            </div>
            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled">
                {{$seller->currency }} &nbsp;
                <small class="bg-warning">{{$seller->buyer_disabled_currency == 0 ? '':  __('de-activated')  }}</small>
            </a>
            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled">
                
                @foreach($seller->delivery_days as $day)
                    <small>{{ substr($days[$day],0, 3) }}</small>
                    @if(!$loop->last)
                        {{__(',')}}
                    @endif
                @endforeach
              &nbsp;
               
            </a>
            <a class="list-group-item list-group-item-action pt-1 pb-1 disabled">
                {{ $payment_frequency[ $seller->payment_frequency]  }} &nbsp;
               
            </a>
        </div>
       
        
       
      
  
@endforeach
@else
    {{__('No seller priced your products yet.')}}
@endif

