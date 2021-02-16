<div class="card">
    <div class="card-header bg-secondary text-light">
        <nav class="nav  nav-pills">
           
                <a class="btn btn-outline-dark   align-items-center text-light"
                   href="/seller/about">{{__('About')}}</a>
                <a class="nav-item nav-link   align-items-center text-light {{ isset($delivery_locations_active)     ? $delivery_locations_active     : ''  }}"
                   href="/seller/about/delivery_locations">
                  {{__('Delivery locations')}}
                </a>
            <a class="nav-item nav-link   align-items-center text-light {{ isset($prices_active)     ? $prices_active     : ''  }}"
               href="/seller/about/prices">
                {{__('Our Prices')}}
            </a>
            <a class="nav-item nav-link   align-items-center text-light {{ isset($our_buyers_active)     ? $our_buyers_active     : ''  }}"
               href="/seller/about/our_buyers">
                {{__('Our Buyers')}}
            </a>
        </nav>
    </div>
    <div class="card-header">
        @if(isset($company->buyer_company_name))
            <nav class="nav  nav-pills">
                <a class="nav-item nav-link  nav-tabs align-items-center {{ isset($company_active)     ? $company_active     : ''  }}"
                   href="/seller/about/{{$company->id}}/buyer">
                    {{__('Company')}} : {{$company->buyer_company_name}}
                </a>
                
                <a class="nav-item nav-link  nav-tabs  align-items-center  {{ isset($product_lists_active)     ? $product_lists_active     : ''  }}"
                   href="/seller/about/{{$company->id}}/product_lists">
                    {{__('Product lists')}}
                </a>
                
            </nav>
        @endif
    </div>
</div>

