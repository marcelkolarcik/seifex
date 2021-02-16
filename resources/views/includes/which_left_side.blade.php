@if(\Auth::guard('buyer')->user())
        @if(explode('_',\Auth::guard('buyer')->user()->role)[0] == 'buyer')
            @if(explode('_',\Auth::guard('buyer')->user()->role)[1] == 'owner')
                @include('includes.buyer.left_side')
            @else
                @include('buyer.staff_includes.left_side')
            @endif
        @endif
@endif
@if(\Auth::guard('seller')->user())
        @if(explode('_',\Auth::guard('seller')->user()->role)[0] == 'seller')
            @if(explode('_',\Auth::guard('seller')->user()->role)[1] == 'owner')
                @include('includes.seller.left_side')
            @else
                @include('seller.staff_includes.left_side')
            @endif
        @endif
@endif
{{--@if(explode('/',$_SERVER['REQUEST_URI'])[1]  == 'staff' )
    @include('buyer.staff_includes.left_side')
@else
    @include('includes.buyer.left_side')
@endif--}}
