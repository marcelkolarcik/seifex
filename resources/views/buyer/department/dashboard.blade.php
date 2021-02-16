@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           
            <div class="col-md-12">
                    @include('buyer.department.nav')
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{__('Current Sellers')}} <small class=" text-grey-500">{{__(\App\Services\StrReplace::currency_underscore($department) )}}</small></h5>
                        @if(session('online_form') == 'no_sellers')
                            <p class="text-danger">{{__('No sellers available')}}</p>
                        @endif
                        @if(session('online_form') == 'no_product_list')
                            <p class="text-danger">{{__('Create product list first !')}}</p>
                        @endif
                    </div>
                   
                        @if(!empty($seller_status))
                                @include('buyer.department.department_sellers')
                        @else
                            {{__('No seller priced your products yet.')}}
                        @endif
                       
                        @if( Auth::guard('buyer')->user()->can('buyer_coordinate_requests', App\ProductList::class))
                        <form  action="{{ URL::to('sellers', $company->id) }}"  method="get" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ Form::text('department',str_replace('_',' ',$department),['class' => 'form-control d-none']) }}
        
                            {{ Form::text('country', isset($company->country) ? $company->country : '',['class' => 'form-control d-none']) }}
        
                            {{ Form::text('county', isset($company->county) ? $company->county : '',['class' => 'form-control d-none']) }}
        
                            {{ Form::text('county_l4', isset($company->county_l4) ? $company->county_l4 : '',['class' => 'form-control d-none']) }}
    
                            {{ Form::text('company_id', $company->id ,['class' => 'form-control d-none']) }}
        
                            <button id="submit"  class="btn btn-outline-primary  form-control mt-3">
                                {{__('Browse')}} {{__('Suppliers')}} {{__(str_replace('_',' ',$department))}} </button>
    
                        </form>
                         @endif
                   
                   
                </div>
            </div>
        </div>
    </div>
@endsection


