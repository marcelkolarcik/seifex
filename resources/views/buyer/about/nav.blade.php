<div class="card">
    <div class="card-header bg-secondary text-light">
        <nav class="nav  nav-pills">
           
                <a class="btn btn-outline-dark   align-items-center text-light"
                   href="/buyer/about">{{__('About')}}</a>
                {{--<a class="nav-item nav-link   align-items-center text-light {{ isset($delivery_locations_active)     ? $delivery_locations_active     : ''  }}"--}}
                   {{--href="/buyer/about/delivery_locations">--}}
                  {{--Delivery locations--}}
                {{--</a>--}}
            <a class="nav-item nav-link   align-items-center text-light {{ isset($product_lists_active)     ? $product_lists_active     : ''  }}"
               href="/buyer/about/product_lists">
                {{__('Our Products')}}
            </a>
            <a class="nav-item nav-link   align-items-center text-light {{ isset($our_sellers_active)     ? $our_sellers_active     : ''  }}"
               href="/buyer/about/our_sellers">
                {{__('Our Sellers')}}
            </a>
           
        </nav>
    </div>
</div>

