

<div class="card">
    {{--<div class="card-header bg-secondary text-light">--}}
      {{----}}
         {{--<nav class="nav nav-pills">--}}
             {{--@foreach($companies as $company)--}}
            {{--<a class="btn btn-outline-light   align-items-center text-light" href="/department/{{$department}}/{{$company[0]->id}}">--}}
                {{--{{__(str_replace('_',' ',$department))}}--}}
            {{--</a>--}}
             {{--<a class="nav-item nav-link text-light {{ isset($products_active) ? $products_active : ''  }}" href="/products/{{$department}}/{{$company[0]->id}}">--}}
                 {{--{{__('Products')}}--}}
             {{--</a>--}}
             {{--@if (\Auth::guard('buyer')->user()->can('buyer_see_orders', App\Order::class))--}}
             {{--<a class="nav-item nav-link text-light {{ isset($orders_active)   ? $orders_active   : ''  }}" href="/orders/{{$department}}/{{$company[0]->id}}">--}}
                {{--{{__('Orders')}}--}}
            {{--</a>--}}
             {{--@endif--}}
             {{--@if (\Auth::guard('buyer')->user()->can('manage_products', App\ProductList::class))--}}
            {{--<a class="nav-item nav-link text-light {{ isset($product_lists_active) ? $product_lists_active : ''  }}" href="/requests/{{$department}}/{{$company[0]->id}}">--}}
                {{--{{__('Cooperation requests')}} &nbsp;--}}
            {{--</a>--}}
             {{--@endif--}}
             {{--@endforeach--}}
        {{--</nav>--}}
       {{----}}
    {{--</div>--}}
    @component('components.main_header')
        <a class="text-light" href="{{ url('/buyer/company',$company->id) }}">{{$company->buyer_company_name}}</a>
    @endcomponent
</div>

