@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
                    <div class="col-md-12 " id="product_forms">
                        <form action="{{ URL::to('product_list/save',str_replace(' ','_',$department)) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-md-12 " id="product_table">
                                <div class="card ">
                                    @component('components.main_header_green')
                                        {{__('Edit product name, by clicking on the name.')}}
                                    @endcomponent
                                    
                                    <div class="card-body">
                                        {{ Form::text('language',$language,['class' => 'd-none']) }}
                                       @include('buyer.product_list.table')
                                        {!! Form::submit( $preferred_lang['short'] !== $selected_lang['short'] ? __('Save') .' '.$selected_lang['long'] : __('Save') ,
                                         
                                         ['name' => 'submitbutton' ,
                                        'class' => 'btn btn-light_green text-grey-600 form-control form-control-sm col-md-3 float-right',
                                        'id' => 'save_list',
                                        'title'=>__('After editing these products, save it for for changes to take effect !')]) !!}
                                       
                                      
                                    </div>
                                        @component('components.main_header_green')
                                            {{__('You can add more products to your list, after you save this one.')}}
                                        @endcomponent
                                </div>
                            </div>
                        </form>
                    </div>
        </div>
       
    </div>

    
@endsection
