

@if(session('message') == 'no default price list')
    <div class="alert alert-danger">
        <h4>{{__('You don\'t have default price list yet !')}}</h4>
    </div>
@elseif(session('message') == 'buyer deleted product list')
    <div class="alert alert-danger">
        <h4>{{__('Buyer deleted product list !')}}</h4>
    </div>
@endif
