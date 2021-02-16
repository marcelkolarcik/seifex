
<br>
<table class="table table-sm d-sm-table-cell table-borderless table-responsive-sm">
    <tr>
@foreach($remaining_currencies as $currency_short => $currency_full)
    @if(($loop->iteration % 7) - 1 == 0)
        <tr>
    @endif
        <td>
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
            </small>&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
    @if($loop->iteration % 7 == 0)
        </tr>
     @endif
   
@endforeach

</table>
