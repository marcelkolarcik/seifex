<div class="card-header mb-2">
    
    <label for="currencies">{{__('Currencies')}} :  </label>
    <br>
  
    @foreach($currencies as $currency)
        {{$currency}}
        @if(!$loop->last)
            ,
        @endif
    @endforeach
</div>

