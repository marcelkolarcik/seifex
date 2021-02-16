
@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           
            <div class="col-md-12">
              {{--  @include('includes.which_nav')--}}
                <div class="card">
                    <div class="card-header bg-secondary text-light">
                           {{__('Price list from :')}} {{$seller_company_name}}.
                    </div>
                    @component('components.label_header_with_button')
                        {{__('You can disable')}} &#9940;
                        {{__('or enable')}}  &#9989;
                        {{__('individual product from this seller, if you are not happy with it !')}}
                        @slot('button')
                            {{$currency}}
                        @endslot
                    @endcomponent
                    @if(isset($price_list))
                    <div class="card-body">
                       
                        <table class="table table-sm table-responsive-sm table-hover">
                           
                            @if (Auth::guard('buyer')->user()->can('de_activate_product', App\ProductList::class))
                            
                            <tr>
                                
                                <td>{{__('Change')}}</td>
                                @else
                                <tr>
                                @endif
                                <td >{{__('Product name')}}</td>
                                <td >{{__('Price per kg')}}</td>
                                <td >{{__('Box size')}}</td>
                                <td >{{__('Box price')}}</td>
                                <td >{{__('Type / Brand')}}</td>
                                <td >{{__('Additional info')}}</td>
                             
                               
                            </tr>
                       
                        @foreach($price_list as $product    =>  $data)
                            
                            <tr>
                                @if (Auth::guard('buyer')->user()->can('de_activate_product', App\ProductList::class))
                                    @if($data['unset'] == 0)
                                        <td >
                                            <span title="{{__('Disable product ?')}}"   class="fas fa-minus-circle text-danger activate"
                                                  class =   " btn btn-sm btn-outline-danger bg-transparent"
                                                    product            =   "{{$product}}"
                                                  seller_company_id  =   "{{$seller_company_id}}"
                                                  buyer_company_id   =   "{{$buyer_company_id}}"
                                                  department         =   "{{$department}}"
                                                  action             =    "remove"
                                                  title_text         =    "{{__('Remove product from price list ?',['product'=>$data['product_name']])}}"
                                                  text               =    "{{__('You can revert your action later !')}}"
                                                  wrong       ="{{__('Something went wrong.')}}"
                                                  later        ="{{__('Please try again later.')}}"
                                            
                                            >&#9940;</span>
                                          
                                        </td>
                                        <td >{{$data['product_name']}}</td>
                                    @elseif($data['unset'] > 0)
                                        <td >
                                            <span title="{{__('Enable product ?')}}" class="fas fa-plus-circle  text-success activate"
                                               product            =   "{{$product}}"
                                               seller_company_id  =   "{{$seller_company_id}}"
                                               buyer_company_id   =   "{{$buyer_company_id}}"
                                               department         =   "{{$department}}"
                                               action             =    "add"
                                               title_text         =    "{{__('Add product to price list ?',['product'=>$data['product_name']])}}"
                                               text               =    "{{__('You can revert your action later !')}}"
                                               wrong              ="{{__('Something went wrong.')}}"
                                               later              ="{{__('Please try again later.')}}">
                                               
                                           &#9989;</span>
                                        </td>
                                        <td class="text-danger">{{$data['product_name']}}</td>
                                    @endif
                                @else
                                    <td >{{$data['product_name']}}</td>
                                @endif
                                
                                @if($data['unset'] >= 0)
                                <td >{{$data['price_per_kg']}}</td>
                                <td >{{$data['box_size']}}</td>
                                <td >{{$data['box_price']}}</td>
                                <td >{{$data['type_brand']}}</td>
                                <td >{{$data['additional_info']}}</td>
                              
                                @endif
                               
                            </tr>
                        @endforeach
                        </table>
                        @else
                            <div class="footer bg-danger text-light text-center mt-2">
                               <p class="card-header">
                                   {{__('Seller doesn\'t have prices for your products at the moment.')}}
                               </p>
                            </div>
                           
                        @endif
                    </div>
                </div>
            
            </div>
        </div>
    </div>
@endsection



