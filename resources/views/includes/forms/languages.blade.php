@if($errors->has('currencies') || $errors->has('languages'))
    <div class="currency_wrapper  pl-4">
@else
   <div class="currency_wrapper {{$creating_company_class}}  pl-4">
@endif
  
    <label class="text-primary">{{__('Please select language you can comunicate in...')}} :</label>
    <div id="languages"> </div>
    @if(isset($company->id))
        <span id="missing_country_languages">
            @if($missing_country_languages)
                @foreach($missing_country_languages as $lang_short => $lang_long)
                    <input class='language language_l' title="{{$lang_long}}"   data-label="{{$lang_long}}" name='languages[]' id={{$lang_short}} type='checkbox' value={{$lang_short}} />
                    <label class='text-success {{$lang_short}}' title="{{$lang_long}}" for={{$lang_short}}> {{$lang_long}} </label>
        
                @endforeach
            @endif
        </span>
       
        @foreach($languages['all'] as $lang_short => $lang_long)
           
        
            <input class='language language_l' title="{{$lang_long}}"  checked data-label="{{$lang_long}}" name='languages[]' id={{$lang_short}} type='checkbox' value={{$lang_short}} />
            <label class='text-orange {{$lang_short}}' title="{{$lang_long}}" for={{$lang_short}}> {{$lang_long}} </label>
        
        @endforeach
    @endif
       <div id="pref_lang_wrapper" class="">
           <label class="text-primary">{{__('Please select your preferred language')}} :</label>
           <div id="preferred_languages">
               @if(isset($company->id))
                
                   @foreach($languages['all'] as $lang_short => $lang_long)
                       <div id="{{$lang_short.'_'}}">
                           <input name="preferred_language"
                                  class="preferred_language"
                                  type="radio"
                                  {{$lang_long === array_values($languages['preferred'])[0] ? 'checked': ''}}
                                  data-language="{{$lang_short}}"
                                  value="{{$lang_short}}">
                           <label for="preferred_language"
                                  id="{{$lang_short.'_label'}}"
                                  class="form-check-label form-check-inline preferred_language
                                   {{$lang_long === array_values($languages['preferred'])[0] ? 'text-orange': 'text-success'}}"
                           >
                               {{__($lang_long)}}
                           </label> &nbsp;&nbsp;&nbsp;
                       </div>
                   @endforeach
               @endif
           </div>
       </div>
    {{-- /*span to load neighbour languages*/--}}
    <span id="more_languages"
          data-country_id="{{  session()->has('selected_country') ?  session()->get('selected_country')   :   isset($company->country) ? $company->country: ''  }}"
          data-who="neighbour"
          class="btn btn-sm btn-link text-right more_languages" >
           {{__('more languages...')}}&#11167;
    </span>
    {{-- /*span to toggle neighbour languages*/--}}
    <span class="btn btn-sm btn-link toggle_languages_div neighbour_languages d-none"
          data-div="neighbour_languages" >
            {{__('more languages...')}} &#11167;&#11165;
        </span>
    
    <div id="neighbour_languages"></div>
    
    {{-- /*span to load remaining languages*/--}}
    <span id="remaining_langs"
          data-country_id="{{  session()->has('selected_country') ?  session()->get('selected_country')   :   isset($company->country) ? $company->country: ''  }}"
          data-who="remaining"
          class="btn btn-sm btn-link text-right d-none more_languages" >
           {{__('extra languages...')}}&#11167;
    </span>
    {{-- /*span to toggle remaining languages*/--}}
    <span class="text-primary toggle_languages_div btn btn-sm btn-link d-none remaining_languages"
          data-div="remaining_languages">
            {{__('extra languages...')}} &#11167;&#11165;
        </span>
    <div id="remaining_languages"></div>
    
    
  
   
</div>

