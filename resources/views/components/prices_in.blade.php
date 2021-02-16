<div class="card-header mb-2">
 
    <label for="prices_in">{{__('Your prices')}} : </label>
    <br>
  
    @foreach($prices_in as $department =>$currencies)
        {{$department }} :
        @foreach($currencies as $currency =>$languages)
       
            {{ $currency}} :
            @foreach($languages as $language => $key)
                {{ str_replace('|',' | ',$language) }}
                @if(!$loop->last)
                    ,
                @endif
            @endforeach
        @endforeach
    @endforeach
</div>

