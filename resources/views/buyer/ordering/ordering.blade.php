@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
                @if($companies[session()->get('company_id')]->price_lists !== null )
                    {{--DEPARTMENTS--}}
                    @include('buyer.ordering.includes.departments')
                    <hr>
                    {{--DELIVERY DAYS--}}
                    @include('buyer.ordering.includes.delivery_days')
                    {{--ALTERNATIVE CURRENCY / LANGUAGE--}}
              
                @else
                    <span>
                    {{__('No seller priced your products, try to find new sellers here ')}}
                <br>
                        @if (Auth::guard('buyer')->user()->can('buyer_coordinate_requests', App\ProductList::class))
                        <a class="list-group-item list-group-item-light_green pt-1 pb-1 mt-1"
                           href="/search_sellers/{{session()->get('company_id')}}">
                            {{__('Search sellers')}}
                        </a>
                        @endif
               </span>
                @endif
                
        <div id="alternative"  class="p-0 col-md-12">  </div>
        <div id="form" class="pl-0 col-md-12"></div>
        
        
        </div>
       
    </div>
@endsection
