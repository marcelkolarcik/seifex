<span class="float-md-right">
@if($order->invoiced_at !=  null)
    {{__('Payment Confirmed ')}}&#10004;
@else
    {{__('Not paid')}}
@endif
</span>

@if($order->buyer_confirmed_delivery_at != null)
    <span class="float-md-left">
    <span class="text-monospace text-primary">
        {{__('Buyer confirmed')}} {{\Carbon\Carbon::parse($order->buyer_confirmed_delivery_at)->diffForHumans()}}
     </span>
    @if($order->comment != null)
        <hr>
        <span class="text-monospace text-primary">   {{__('Comment :')}} </span>
        <span > {{$order->comment}} </span>
        
    @endif
</span>
@endif
