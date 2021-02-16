{{--<button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#seller_prices">--}}
   {{--{{__('My Prices')}}--}}
{{--</button>--}}

<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="seller_prices" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{__('Default prices')}} {{str_replace('_',' ',session('department'))}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="product_prices" >
                    {{--extended table--}}
                    <table class="table table-condensed table-bordered table-striped table-sm" id="default_seller_prices">
                        <tr>
                            
                            <td><b>{{__('Product')}} {{__('name')}}</b></td>
                            <td><b>{{__('Product')}} {{__('code')}}</b></td>
                            <td ><b>{{__('Price per kg')}}</b></td>
                            <td >{{__('Type / Brand')}}</td>
                            <td ><b>{{__('Stock level')}}</b></td>
                            <td >{{__('Size of unit in kg/l')}}</td>
                            <td ><b>{{__('Price per box')}}</b></td>
                            <td >{{__('Additional info')}}</td>
                            <td >{{__('Unset')}}</td>
                           
                        </tr>
                       
                           
                            @if(isset($info->prices_for_modal) )
                                @foreach($info->prices_for_modal as $long_product_name => $product_data)
                                    @if(isset($product_data['translation_needed']))
                                    
                                    {{--<button class="btn btn-sm bg-warning">--}}
                                        {{--{{\App\Services\Language::get_language_names([session()->get('language')],'short_long')['long']}}--}}
                                        {{--&nbsp;{{__('translation needed')}}--}}
                                    {{--</button>--}}
                                        @endif
                                <tr >
                                   
                                    @foreach($product_data as $product_detail => $product_desc)
                                 
                                        @if($product_detail != 'old_hash_name' && $product_detail != 'translation_needed' )
                                        <td class={{ isset($product_data['translation_needed']) ? 'bg-warning' : ' '}}>
                                        
                                             {{$product_desc}}
                                        
                                        </td>
                                        @endif
                                   
                                    @endforeach
                                </tr>
                        
                        @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
        </div>
    </div>
</div>
{{--END OF MODAL--}}
