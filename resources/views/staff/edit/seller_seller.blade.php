@include('staff.includes.staff_details')
        <div class="col-md-6">
            @component('components.label_header_primary')
                {{__('Locations.')}} <br>
                {{__('To modify location, simply delete it and replace it with new one !')}} <br>
            @endcomponent
            @if($scope)
                @foreach( $scope['last_locations'] as     $base_location => $levels_locations)
                @foreach($levels_locations as $level   =>  $locations)
                    @foreach($locations as $location)
                        <div   class=" list-group-item  pl-1 pt-1 pb-0  col-md-12 {{str_replace('.','_',$location) }}">
                            <span class="delete_location text-danger small btn btn-sm btn-outline-danger pt-0 pb-0 float-md-right"
                                  data-location_id="{{ str_replace('.','_',$location)  }}"
                            title="{{__('Delete location ?')}}">
                                {{__('delete')}}
                            </span>
                           
                        @if($level === 'country')
                              
                                {!! Form::checkbox('base_locations[]',$base_location,true,['class'=>'d-none','readonly' ] ) !!}
                                {!! Form::checkbox('countries['.$location.'][]',$location,true,['class'=>'d-none','readonly' ] ) !!}
        
                                {!! Form::label('location',$location_names[$location]['location_name']  ,['class' => '']) !!}
                               
                            @endif
                            @if($level === 'county')
                               
                                {!! Form::checkbox('base_locations[]',$base_location,true,['class'=>'d-none','readonly' ] ) !!}
                                {!! Form::checkbox('counties['.explode('.',$location)[0].'.'.explode('.',$location)[1].'][]',$location,true,
								 ['class'=>'d-none','readonly' ] ) !!}
                                
                                    {!! Form::label('location',$location_names[$location]['location_name']  ,['class' => '']) !!}
                                    {!! Form::label('location',
                                    substr($location_names[$location]['path'],
                                    strpos($location_names[$location]['path'], ':') + 1)
                                    ,['class' => 'small text-grey-500']) !!}
                               
                            @endif
                            @if($level === 'county_l4')
                              
                                {!! Form::checkbox('base_locations[]',$base_location,true,['class'=>'d-none','readonly' ] ) !!}
                                {!! Form::checkbox('counties_l4['.explode('.',$location)[0].'.'.explode('.',$location)[1].'.'.explode('.',$location)[2].'][]',$location,true,
								 ['class'=>'d-none','readonly' ] ) !!}
        
                                {!! Form::label('location',$location_names[$location]['location_name']  ,['class' => '']) !!}
                                {!! Form::label('location',
								substr($location_names[$location]['path'],
								strpos($location_names[$location]['path'], ':') + 1)
								,['class' => 'small text-grey-500']) !!}
                            
                            @endif
                               </div>
                    @endforeach
                @endforeach
            @endforeach
            @endif
        </div>
        <div class="col-md-3">
            @include('staff.includes.languages')
        </div>
        
        <div class="col-md-3">
            @include('staff.includes.departments')
        </div>
        <div class="col-md-12">
            @component('components.label_header_primary')
                {{__('Add new locations')}}
            @endcomponent
        
        </div>
        <div class="col-md-4">
            @include('staff.includes.locations')
        
        </div>
        <div class="col-md-4">
            <div id="load" >
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="included_locations d-none">
                @component('components.label_header_primary')
                    {{__('Included locations')}}
                @endcomponent
            </div>
          
            <div id="new_staff" class=" m-0 p-0 text-secondary" >
            
            </div>
           @include('staff.includes.save_button')
        </div>
       
