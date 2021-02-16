
@if($errors->has('currencies') || $errors->has('languages'))
    <div class="currency_wrapper pl-4">
@else
    <div class="currency_wrapper {{$creating_company_class}}  pl-4">
@endif
        
        <div id="currently_selected" class="d-none">
            <label class="text-primary">{{__('Currency of selected country')}} :</label>
            <label  class="new_currency"></label>
        </div>
        <label class="text-primary" >
            {{ Auth::guard('seller')->check() ?
            __('Select currencies you are able to receive payment in')
            : __('Select currencies you are able to pay with') }}
            
        
        </label>
        <br>
        <div id="currencies"> </div>
                @if(isset($company->id))
                    <span id="missing_country_currency">
                        @if($missing_country_currency)
                            @foreach($missing_country_currency as  $currency_short => $currency_full)
                                <small title="{{__(explode('_',$currency_full)[1])}}">
                                    <input name="currencies[]" class=" seifex_currency {{$currency_short}}"
                                           data-display="{{__(\App\Services\StrReplace::currency_underscore($currency_full))}}"
                                           type="checkbox"
                                           
                                           value="{{$currency_short}}">
                
                                    <label  class="form-check-label text-success {{$currency_short}}"  >
                                        {{$currency_short}}&nbsp;
                                        {{--<small>
											{{__(explode('_',$currency)[1])}}
										</small>--}}
                                    </label>
                                </small>&nbsp;&nbsp;
                            @endforeach
                         @endif
                        
                    </span>
                     @foreach($company_currencies as  $currency_short => $currency_full)
                <small title="{{__(explode('_',$currency_full)[1])}}">
                    <input name="currencies[]" class=" seifex_currency {{$currency_short}}"
                           data-display="{{__(\App\Services\StrReplace::currency_underscore($currency_full))}}"
                           type="checkbox"
                           checked
                           value="{{$currency_short}}">
                    
                    <label  class="form-check-label text-orange {{$currency_short}}"  >
                        {{$currency_short}}&nbsp;
                        {{--<small>
							{{__(explode('_',$currency)[1])}}
						</small>--}}
                    </label>
                </small>&nbsp;&nbsp;
                    @endforeach
                @endif
        <br>
        @if(session('check'))
            <label for="preferred_currency" class="text-danger" >{{__('Please, select your preferred currency')}}</label>
        @else
            <label for="preferred_currency" class="text-primary"  >{{__('Please, select your preferred currency')}}</label>
        @endif
        <div class="form-check " id="preferred_currency">
        
            @if(isset($company->id))
          
                @foreach($company_currencies as $currency_short => $currency_full)
                    <div id="{{$currency_short}}">
                        <input name="preferred_currency" class="form-check-input  preferred_currency" data-currency="{{$currency_short}}"
                               type="radio"
                               {{$currency_short === array_key_first($preferred_currency) ? 'checked': ''}}
                    
                               value="{{$currency_short}}">
                        <label for="preferred_currency" class="form-check-label form-check-inline preferred_currency
                         {{$currency_short === array_key_first($preferred_currency) ? 'text-orange': 'text-success'}}"
                               id="{{$currency_short.'_label'}}"
                        >
                            {{__(\App\Services\StrReplace::currency_underscore($currency_full))}}</label> &nbsp;&nbsp;&nbsp;
                    </div>
            
                @endforeach
            @endif
        </div>
    <div class="neighbour_currencies_wrapper">
       
        {{-- /*span to load neighbour currencies*/--}}
        <span id="more_currencies"
              data-country_id="{{  session()->has('selected_country') ?  session()->get('selected_country')   :   isset($company->country) ? $company->country: '' }}"
              data-who="neighbour"
              class="btn btn-sm btn-link more_currencies " >
           {{__('more currencies...')}}&#11167;
        </span>
        <span class="glyphicon glyphicon-bell"></span>
        {{-- /*span to toggle neighbour currencies*/--}}
        <span class="btn btn-sm btn-link toggle_currencies_div neighbour_currencies d-none"
              data-div="neighbour_currencies" >
            {{__('more currencies...')}} &#11167;&#11165;
        </span>
       
        <div id="neighbour_currencies" ></div>
     
        {{-- /*span to load remaining currencies*/--}}
        <span id="remaining_curr"
              data-country_id="{{  session()->has('selected_country') ?  session()->get('selected_country')   :   isset($company->country) ? $company->country: ''  }}"
              data-who="remaining"
              class="btn btn-sm btn-link d-none more_currencies" >
           {{__('extra currencies...')}}&#11167;
        </span>
        {{-- /*span to toggle remaining currencies*/--}}
        <span class="text-primary toggle_currencies_div btn btn-sm btn-link d-none remaining_currencies"
              data-div="remaining_currencies">
            {{__('extra currencies...')}}&#11167;&#11165;
        </span>
        
    </div>
       
       
          <div id="remaining_currencies" ></div>
    
          
    
   
</div>

