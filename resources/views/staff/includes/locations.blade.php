
@foreach( $details['locations'] as     $location)
   
        <span   class=" list-group-item  pl-1 pt-1 pb-0  col-md-12 ">
                    
                    {!! Form::checkbox('base_locations[]',$location['location_id'],false,
                    ['class'=>'base_location',
                    'data-location_name'=> array_key_first($location['path']) ] ) !!}
            
            {!! Form::label('base_location', \App\Services\StrReplace::dash( array_key_first($location['path'])) , ['class' => 'text-dark']) !!}
            <label for="base_location" class="small text-grey-500">
                {{ \App\Services\StrReplace::dash( array_values($location['path'])[0]) }}
            </label>
            
         </span>
  
@endforeach
