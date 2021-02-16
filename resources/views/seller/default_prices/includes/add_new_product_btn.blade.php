<button
    num_of_products = "{{isset($a ) ? $a : 1 }}"
    class="btn btn-success btn-sm add_new_product_btn"
    optional = "{{__('optional')}}"
    req = "{{__('required')}}"
>{{__('add new product')}}
</button>
