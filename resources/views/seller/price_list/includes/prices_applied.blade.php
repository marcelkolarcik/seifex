<div class="card ">
    <form  action="{{ URL::to('/price_list/store') }}"  method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ Form::text('currency',session()->get('currency'), array( 'class' => 'd-none')) }}
        {{ Form::text('language', session()->get('language'),array( 'class' => 'd-none')) }}
        
        <div class="card-header" id="actions">
            @include('seller.price_list.includes.actions')
        </div>
        
        <div id="product_prices" >
            <table class="table table-condensed table-bordered" id="multi_seller_prices">
                @include('seller.price_list.includes.table_head')
                
                @if (!empty($seller_price_list_multi))
                    @component('components.main_header_green')
                        {{__('Select from multiple choices')}}
                    @endcomponent
                    @foreach($seller_price_list_multi as $product_name => $products_multi)
                        
                        <?php
                        $select_data=[];
                        foreach($products_multi as $key=>$product_data)
                        {
                            
                            if(!empty($product_data['type_brand'])) {$type_brand = ' | '.$product_data['type_brand'] ;} else{$type_brand = '';}
                            if(!empty($product_data['box_size'])) {$box_size = ' | '.$product_data['box_size'].__(' kg/l size') ;} else{$box_size = '';}
                            if(!empty($product_data['box_price'])) {$box_price = ' | '.$product_data['box_price'].__(' unit price ') ;} else{$box_price = '';}
                            if(!empty($product_data['additional_info'])) {$additional_info = ' | '.$product_data['additional_info'] ;} else{$additional_info = '';}
                            /*here add old hash name as first element*/
                            
                            $select_data[
                            str_replace(' ','_',$product_data['old_hash_name'])
                            .'|'.str_replace(' ','_',$product_data['product_name'])
                            .'|'.$product_data['product_code']
                            .'|'.$product_data['price_per_kg']
                            .'|'.$product_data['type_brand']
                            .'|'.$product_data['box_size']
                            .'|'.$product_data['box_price']
                            .'|'.$product_data['additional_info']
                            .'|'.$product_data['unset']] =
                                
                                $product_data['product_name']
                                .$product_data['product_code']
                                .' '.$product_data['price_per_kg'].__(' per kg ')
                                .$type_brand
                                .$box_size
                                .$box_price
                                .$additional_info
                            ;
                        }
                        ?>
                        
                        <tr>
                            
                            <select required id="{{str_replace(' ','_',$product_name)}}"
                                    class="form-control form-control-sm multi mt-1 col-md-4 text-danger" >
                                <option value="0">{{__('Select price for')}} {{$product_name}}</option>
                                @foreach($select_data as $short=>$long)
                                    <option value={{$short}} > {{$long}}</option>
                                @endforeach
                            </select>
                        </tr>
                    
                    @endforeach
                    <br/>
                @endif
                @include('seller.price_list.includes.seller_price_list')
               
            
            </table>
            <div id="update_prices">
                @include('seller.price_list.includes.submit_button')
            </div>
        </div>
    
    </form>
    <div class="footer">
    
    </div>
</div>
