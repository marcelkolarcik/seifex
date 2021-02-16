@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           {{-- <div class="col-md-3 ">
                @include('includes.which_left_side')
            </div>--}}
            <div class="col-md-12 ">
                @include('includes.feedback')
              <div class="accordion" id="new_locations">
                <div class="card ">
                    <div class="card-header bg-secondary text-light">
                        <ul class="nav nav-tabs ">
                            <li class="nav-item">
                                <a class="nav-link  {{isset($locations_active) ? $locations_active : 'text-light' }}"
                                   href="/delivery_locations">{{__('Delivery locations')}}</a>
                            </li>
                            @if (Auth::guard('seller')->user()->can('add_delivery_locations', App\DeliveryLocation::class))
                                @if($company->price_lists_extended['price_lists_extended'] != null)
                            <li class="nav-item">
                                <a data-toggle="collapse"
                                   data-target="#collapse"
                                   aria-expanded="true"
                                   aria-controls="collapse"
                                   class="nav-link {{isset($add_location_active) ? $add_location_active : 'text-light' }}"
                                   href="#">{{__('Add locations')}}</a>
                            </li>
                        </ul>
                        @else
                            <hr>
                            <span class="text-light_green mr-2">
                                {{__('Please, create price list, before you add delivery locations. Thank you!' )}}
                            </span>
                            <span>
                                <a class="list-group-item list-group-item-action pt-1 pb-1" href="\prices">{{__('Create prices')}}</a>
                            </span>
                        @endif
                        @endif
                    </div>
                    <div id="collapse" class="collapse" aria-labelledby="{{'Add locations'}}" data-parent="#new_locations">
                       
                            <form  action="{{ URL::to('expand_delivery_locations') }}"  method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="list-group list-group-horizontal-sm mb-sm-1 mb-1" >
                                    <a class="list-group-item list-group-item-action pt-1 pb-1  text-grey-800 "  >
                                        
                                        @include('includes.forms.locations')
                                    </a>
                                    <a class="list-group-item list-group-item-action pt-1 pb-1  text-grey-800 "  >
                                        <label for="delivery_days">{{__('Delivery days for location')}}</label>
                                        @include('includes.forms.delivery_days')
                                       
                                    </a>
                                    <a class="list-group-item list-group-item-action pt-1 pb-1  text-grey-800 "  >
                                        <label for="department"> {{__('Department')}}</label>
                                        {{ Form::select('department', $departments,null,['class' => 'form-control form-control-sm','placeholder'    =>  __('Please select department !') , 'required']) }}
                                        <button class="btn btn-primary  form-control mt-3">{{__('Add new location')}}</button>
                                    </a>
                                </div>
                               
                                {{ Form::text('seller_company_id', $seller_company_id,['class' => 'd-none']) }}
                            </form>
                           
                       
                    </div>
                </div>

                        @include('seller.company.delivery.delivery_locations')
                </div>
            </div>

        </div>
    </div>
@endsection
