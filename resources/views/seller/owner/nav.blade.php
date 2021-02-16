
<div class="card">
    <div class="list-group list-group-horizontal-sm" id="myList" role="tablist">
        <a class="list-group-item list-group-item-action active pt-1 pb-1 "  href="/seller" >
            {{session()->get('seller_company_name')}}
        </a>
        {{--<a class="staff_link list-group-item list-group-item-action active pt-1 pb-1" data-toggle="list" href="#seller_seller" role="tab">Sales</a>--}}
        {{--<a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#seller_accountant" role="tab">Accounts</a>--}}
        {{--<a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#seller_delivery" role="tab">Delivery</a>--}}
        {{--<a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#previous" role="tab">Previous</a>--}}
    </div>
    {{--<div class="card-header bg-secondary text-light">--}}
        {{--<nav class="nav  nav-pills">--}}
            {{--<a class="btn btn-outline-dark   align-items-center text-light"--}}
               {{--href="/seller">{{session()->get('seller_company_name')}}</a>--}}
            {{--<order-notification :data-project="{{session('seller_company_id')}}"></order-notification>--}}
            {{--<a class="nav-item nav-link   align-items-center text-light {{ isset($orders_active)     ? $orders_active     : ''  }}"--}}
               {{--href="/orders">--}}
                {{--{{__('Orders')}}--}}
               {{----}}
            {{--</a>--}}
            {{----}}
            {{--<a class="nav-item nav-link d-flex justify-content-between align-items-center text-light {{ isset($product_lists_active) ? $product_lists_active : ''  }}"--}}
               {{--href="/requests">--}}
                {{--{{__('Cooperation requests')}}--}}
               {{----}}
            {{--</a>--}}
            {{--<a class="nav-item nav-link d-flex justify-content-between align-items-center text-light"--}}
               {{--href="/invoices">--}}
              {{--{{__('Invoices')}}--}}
            {{--</a>--}}
        {{--</nav>--}}
    {{--</div>--}}
</div>
