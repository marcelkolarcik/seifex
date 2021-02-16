<div class="card">
    <div class="card-header bg-secondary text-light">
        <nav class="nav  nav-pills">
            {{--@foreach($companies as $company)--}}
                {{--<a class="btn btn-outline-dark   align-items-center text-light"--}}
                {{--href="/seller/company/{{$company[0]->id}}">{{$company[0]->seller_company_name}}  </a>--}}
               {{----}}
                {{--@if(isset($staff_duties['orders']))--}}
                {{--<a class="nav-item nav-link   align-items-center text-light {{ isset($orders_active)     ? $orders_active     : ''  }}"--}}
                   {{--href="/orders/{{$company[0]->id}}">--}}
                    {{--@include('includes.orders_count')--}}
                {{--</a>--}}
                {{--@endif--}}
                {{--@if(isset($staff_duties['requests']))--}}
                {{--<a class="nav-item nav-link d-flex justify-content-between align-items-center text-light {{ isset($product_lists_active) ? $product_lists_active : ''  }}"--}}
                   {{--href="/requests/{{$company[0]->id}}">--}}
                    {{--@include('includes.cooperation_requests_count')--}}
                {{--</a>--}}
                {{--@endif--}}
           {{----}}
                {{--@if(isset($staff_duties['invoices']))--}}
                    {{--<a class="nav-item nav-link d-flex justify-content-between align-items-center text-light {{ isset($invoices_active) ? $invoices_active : ''  }}"--}}
                       {{--href="/invoices">--}}
                       {{--{{__('Invoices')}}--}}
                    {{--</a>--}}
                {{--@endif--}}
            {{--@endforeach--}}
        </nav>
    </div>
</div>

