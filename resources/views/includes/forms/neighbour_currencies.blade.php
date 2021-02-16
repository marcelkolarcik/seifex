


<br>
{{--<table class="table table-sm table-borderless table-responsive-sm">
    <tr>--}}
@foreach($neighbour_currencies as $currency_short => $currency_full)
    @if(($loop->iteration % 6) - 1 == 0)
       {{-- <tr>--}}
    @endif
            {{--<td>--}}
            <span title="{{__(explode('_',$currency_full)[1])}}">
            <input name="currencies[]" class=" seifex_currency {{$currency_short}}"
                   data-display="{{__(\App\Services\StrReplace::currency_underscore($currency_full))}}"
                   type="checkbox"

                   value="{{$currency_short}}">
                <small>
                 <label  class=" text-success {{$currency_short}}"  >
                     {{$currency_short}}&nbsp;
                     {{--<small>
                         {{__(explode('_',$currency)[1])}}
                     </small>--}}
                 </label>
                </small>
            </span>&nbsp;&nbsp;&nbsp;&nbsp;
            {{--</td>--}}
            @if($loop->iteration % 6 == 0)
       {{-- </tr>--}}<br>
    @endif
    
@endforeach

{{--</table>--}}
