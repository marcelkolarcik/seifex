@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           {{-- <div class="col-md-3">
                <div class="list-group">
                    @include('includes.buyer.left_side')
                </div>
            </div>--}}
                    <div class="col-md-12 " id="product_forms">@include('includes.feedback')
                         <form action="{{ URL::to('product_list/check') }}" method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                             
                             <div class="list-group list-group-horizontal-sm" >
                                 <div class="accordion" id="accordion_info">
                                     <a class="list-group-item list-group-item-action  bg-grey-300 text-grey-800" role="tab"
                                        data-toggle="collapse" data-target="#info" aria-expanded="true" aria-controls="info">
                                         {{__('How to')}}
                                     </a>
                                 </div>
                                 <div class="list-group-item list-group-item-action  pt-1 pb-1 bg-secondary text-light"   role="tab" >
                                     <label for="department_div">{{__('Select department')}}</label>
    
                                     {{ Form::select('department',$default_departments, isset($department) ?   str_replace('_',' ',$department) : null,
									 ['class' => 'form-control form-control-sm  product_list',
									 'id' => 'buyer_product_list_departments',
									'required',
									 'placeholder'=>__('Please select department')]) }}
                                 </div>
                                 <div class="list-group-item list-group-item-action pt-1 pb-1 bg-secondary text-light "   role="tab" >
                                     @if(sizeof($languages['all']) > 1)
                                         
                                             <label for="languages">{{__('Select language')}}</label>
                                             {{ Form::select('language',$languages['all'], array_key_first($languages['preferred']) ,
											 ['class' => 'form-control form-control-sm  product_list',
											 'id' => 'buyer_languages',
											 'required'=>'required',
											 'placeholder'=>__('Please select language')]) }}
                                     
                                     @else
                                         {{ Form::text('language',array_key_first($languages['preferred']),
											['class' => 'd-none', 'id' => 'buyer_languages',]) }}
    
    
                                     @endif
                                 </div>
                               
                                
                                 <div class="list-group-item list-group-item-action pt-1 pb-1 bg-secondary text-light"  >
                                     <button type="button" class="btn  btn-danger btn-sm  float-right d-none"
                                             id="delete_department"
                                             buyer_company_id="{{$company->id}}"
                                             title           ="{{__('Do you want to delete')}}"
                                             department      =""
                                             wrong       ="{{__('Something went wrong.')}}"
                                             later        ="{{__('Please try again later.')}}"
                                     >
                                         {{__('Delete department')}}
    
                                     </button>
                                 </div>
                             </div>
                                    <div class="card ">
                                        <div id="info" class="collapse"  data-parent="#accordion_info">
                                            <div class="card-body">
                                                <label for="product_div"> {{__('Type or paste your product list separated by comma')}}
                                                    <code>,</code>!</label><br>
                                                {{__('Be specific or general, depending what you want. For example: If you want to buy ')}}<code class="text-light_green bg-grey-800">{{__(' any ')}}</code>{{__(' apple, write ')}}<code class="text-light_green bg-grey-800">{{__(' apple ')}}</code>.
                                                <br>
                                                {{__('If you want')}} <code class="text-light_green bg-grey-800">{{__(' specific ')}}</code> {{__('type of an apple, like for example Gala apple, write')}} <code class="text-light_green bg-grey-800">{{__(' apple gala ')}}</code>
                                            </div>
                                        </div>
                                        
                                     
                                        
                                        <div id="changed_products">
                                            @include('buyer.product_list.changed_products')
                                        </div>
                                       
                                        <div id="product_list_current">
                                        
                                        </div>
                                       
                                            {{-- BUYER CAN ADD PRODUCTS ONLY IN PREFERRED LANGUAGE--}}
                                           
                                            <div class="form-group  product_div">
                                                {{ Form::textarea('new_products', isset($product_list) ?  $product_list : null,
                                                 ['rows' => '3',
                                                 'id' => 'product_list' ,
                                                
                                                 'class' => 'form-control d-none',
                                                 'placeholder'=>__('Type or paste your product list separated by comma "," here!')]) }}
                                            </div>
                                          
                                            <div class="form-group  check_div">
                                                {!! Form::submit(__('Check your list'),
                                                 ['name' => 'submitbutton' , 'class' => 'btn btn-outline-primary form-control check_div d-none']) !!}
                                            </div>
                                    </div>
                         </form>
                    </div>
        </div>
    </div>
    
@endsection
