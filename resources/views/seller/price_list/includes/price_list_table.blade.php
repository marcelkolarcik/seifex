{{--//@include('seller.price_list.includes.nav')--}}
<div class="card ">
  
    <form  action="{{ URL::to('/price_list/store') }}"  method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
       
        <div class="card-header" id="actions">
            @include('seller.price_list.includes.actions')
        </div>
        <div id="product_prices" >
            <table class="table table-condensed table-bordered" id="multi_seller_prices">
                @include('seller.price_list.includes.table_head')
                @include('seller.price_list.includes.seller_price_list')
                        @if($info->match)
                            @if(sizeof($info->new_products)>0)
                                @component('components.label_header_with_button_no_mb')
                                    You do not have products in green. You can price product list without them or you can add them to your price list.
                                    @slot('button')
                                        <a href="/prices?new_products=1" class="btn btn-sm    btn-light_green text-primary"> Add</a>
                                    @endslot
                                @endcomponent
                            @endif
                        @endif
            </table>
            @if (Auth::guard('seller')->user()->can('price_product_list', App\ProductList::class))
                <div id="update_prices">
                    @if(isset($info->match) && $info->match != false)
                        @include('seller.price_list.includes.submit_button')
                    @endif
                </div>
            @endif
        </div>
    </form>
</div>
