<hr>
<table class="table table-sm table-borderless table-responsive-sm">
    <tr>
@foreach($remaining_languages as $lang_short => $lang_long)
       {{-- @if(($loop->iteration % 6) - 1 == 0)
            
            <tr>
                @endif
                <td>--}}
                    <small>
                <input class='language language_l' title="{{$lang_long}}"  data-label="{{$lang_long}}" name='languages[]' id={{$lang_short}} type='checkbox' value={{$lang_short}} />
                <label class='text-success {{$lang_short}}' title="{{$lang_long}}" for={{$lang_short}}> {{$lang_long}} </label>
                    </small>&nbsp;&nbsp;&nbsp;&nbsp;
               {{-- </td>
                @if($loop->iteration % 6 == 0)
            </tr>

@endif--}}
@endforeach
</table>
