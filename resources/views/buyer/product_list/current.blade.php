{{--@if($product_list === [])--}}
    {{--@component('components.main_header_green')--}}
        {{--{{__(' Please add your products  in your preferred language first!')}} ({{$preferred_lang['long']}})--}}
    {{--@endcomponent--}}

@if($product_list !== null || $product_list != [])
  
    @if($preferred_lang['short'] !== $selected_lang['short'])
      
            @component('components.main_header_green')
                {{__('Feel free to translate your products to your language ')}} ({{$selected_lang['long']}})
            @endcomponent
    @endif
    @if($preferred_changed)
        @component('components.main_header_red')
            {{__('Preferred language changed, you need to have translation in :language ! ',['language'=>$selected_lang['long']])}}{{--translator_language--}}
        @endcomponent
    @endif
    @include('buyer.product_list.table')
@else
 <div>
     @if($preferred_lang['short'] !== $selected_lang['short'])
     
         @component('components.main_header_green')
             {{__(' Please add your products  in your preferred language first!')}} ({{$preferred_lang['long']}})
         @endcomponent
     @else
         @component('components.main_header_green')
             {{__(' Please add your products bellow!')}}
         @endcomponent
     @endif
 </div>

@endif
