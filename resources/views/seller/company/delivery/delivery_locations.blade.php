@if(isset($delivery_locations))

        @foreach($delivery_locations as $department => $locations)
            <div class="list-group list-group-horizontal-sm mb-sm-1 mb-1" >
                <a class="list-group-item list-group-item-action pt-1 pb-1 bg-grey-300 text-grey-800 disabled"  >
                    {{__(str_replace('_',' ',$department))}}
                </a>
                <a class="list-group-item list-group-item-action pt-1 pb-1 bg-grey-300 text-grey-800 disabled"  >
                    {{__('Delivery days')}}
                </a>
                <a class="list-group-item  pt-1 pb-1 bg-grey-300 text-grey-800 disabled"  >
                  <button class="btn btn-sm btn-outline-grey-800 disabled">
                      {{__('Update')}}
                  </button>
                  
                </a>
               
                <a class="list-group-item  pt-1 pb-1 bg-danger text-light "  >
                    @if (Auth::guard('seller')->user()->can('delete_delivery_locations', App\DeliveryLocation::class))
                        <button title="{{__('Are you canceling delivery for')}}  {{__(str_replace('_',' ',$department))}} {{__('department')}} ?"
                                class="btn btn-danger btn-sm ddd"
                                department=" {{str_replace(' ','_',$department)}}"
                                seller_company_id="{{$seller_company_id}}"
                                text              = "{{__('Did you notify your buyers ?')}}"
                                wrong              ="{{__('Something went wrong.')}}"
                                later              ="{{__('Please try again later.')}}"
                        >
                            X
                        </button>
                        @else
                        &nbsp;
                    @endif
                </a>
            </div>
            @foreach($locations as $location_id =>   $location)
                <div class="list-group list-group-horizontal-sm mb-sm-1" >
                <a class="list-group-item list-group-item-action pt-1 pb-1 " href="/delivery/location/{{$location_id}}/{{$department}}" >
                    {{__('id : ')}}  {{$location_id}}
                    <label for="base">
        
                        {{ array_key_first($location['path'])}}
    
                    </label>
                    @if(array_values($location['path'])[0] != '')
                        <label  class="small text-grey-500">
            
                            {{ array_values($location['path'])[0] }}
                        </label>
                    @endif
                    <small class="float-md-right">
                        {{  !isset($buyers_in_location[$location_id])  ? '' :  sizeof($buyers_in_location[$location_id]).__(' buyers') }}
                    </small>
                </a>
                <a class="list-group-item list-group-item-action pt-1 pb-1 "  >
                    @include('includes.forms.delivery_days')
                </a>
                <a class="list-group-item pt-1  pb-1 "  >
                    @if (Auth::guard('seller')->user()->can('edit_delivery_days', App\DeliveryLocation::class))
                        <button class="btn btn-sm btn-outline-success update_location_delivery_days"
                                title="{{__('Update delivery days, by clicking on those checkboxes !')}}"
                                id="{{$location_id}}"
                                data-department = "{{ str_replace(' ','_',$department)}}"
                                data-text="{{__('Update delivery days for :')}}"
                                data-wrong              ="{{__('Something went wrong.')}}"
                                data-later              ="{{__('Please try again later.')}}"
                                data-location_name = " {{ !is_array($location['path'])  ? $location['path'] :  array_key_first($location['path']) .' | '. array_values($location['path'])[0] }}">
                            {{__('Update')}}
                        </button>
                        @else
                        &nbsp;
                    @endif
                </a>
                <a class="list-group-item  pt-1 pb-1  "  >
                    @if (Auth::guard('seller')->user()->can('delete_delivery_locations', App\DeliveryLocation::class))
                        <button
                            title="{{__('Are you canceling delivery for ')}}"
                            class="btn btn-outline-danger btn-sm ddl"
                            delivery_location_id="{{$location_id}}"
                            department="{{$department}}"
                            text      =" {{ !is_array($location['path'])  ? $location['path'] :  array_key_first($location['path']) .' | '. array_values($location['path'])[0] }}"
                            wrong              ="{{__('Something went wrong.')}}"
                            later              ="{{__('Please try again later.')}}"
        
                        ><small>X</small>
                        </button>
                    @else
                    &nbsp;
                    @endif
                </a>
                </div>
            @endforeach
        @endforeach

@endif
