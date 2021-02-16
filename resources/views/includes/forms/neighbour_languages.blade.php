<hr>

@foreach($neighbour_languages as $lang_short => $lang_long)
   
                <input class='language language_l' title="{{$lang_long}}"  data-label="{{$lang_long}}" name='languages[]' id={{$lang_short}} type='checkbox' value={{$lang_short}} />
                <label class='text-success {{$lang_short}}' title="{{$lang_long}}" for={{$lang_short}}> {{$lang_long}} </label>
           
   
@endforeach

