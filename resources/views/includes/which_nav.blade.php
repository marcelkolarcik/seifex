@if(\Auth::guard('buyer')->user())
   @include('buyer.owner.nav')
@endif

@if(\Auth::guard('seller')->user())
    @include('seller.owner.nav')
@endif
