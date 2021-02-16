<div class="img-thumbnail">
	@foreach($info->days as $key => $day)
        {{-- SELLER'S DELIVERY DAYS FOR BUYER --}}
    @if(isset($location) && $location['delivery_days'] !== 'null' )
            
            <div class="form-check form-check-inline delivery_days">
                @if(in_array($key,$location['delivery_days']))
                    
                    <input name="delivery_days_{{$location_id}}" class="form-check-input days" type="checkbox"  value="{{$key}}" checked>
                    <label class="form-check-label text-success" >{{__($day)}}</label>
                @else
                    <input name="delivery_days_{{$location_id}}" class="form-check-input days" type="checkbox"  value="{{$key}}">
                    <label class="form-check-label " >{{__($day)}}</label>
                @endif
            </div>
        @elseif (isset($info->delivery_days) )
           
            <div class="form-check form-check-inline delivery_days text-dark">
                @if(in_array($key,$info->delivery_days))
                   
                   
                    <input name="delivery_days[]" class="form-check-input days " type="checkbox"  value="{{$key}}" checked>
                    <label class="form-check-label text-success" >{{__($day)}}</label>
                
                @else
                    
                    <input name="delivery_days[]" class="form-check-input days" type="checkbox"  value="{{$key}}">
                    <label class="form-check-label " >{{__($day)}}</label>
                
                @endif
                
            </div>
          
        {{-- SELLER'S DELIVERY DAYS FOR LOCATION --}}
       {{-- @elseif(isset($location) && $location->delivery_days !== 'null' )
          
            <div class="form-check form-check-inline delivery_days">
                @if(in_array($key,json_decode($location->delivery_days,true)))
                    
                    <input name="delivery_days_{{$location_id}}" class="form-check-input days" type="checkbox"  value="{{$key}}" checked>
                    <label class="form-check-label text-success" >{{__($day)}}</label>
                @else
                    <input name="delivery_days_{{$location_id}}" class="form-check-input days" type="checkbox"  value="{{$key}}">
                    <label class="form-check-label " >{{__($day)}}</label>
                @endif
            </div>--}}
           
        {{-- SELLER'S DEFAULT DELIVERY DAYS  --}}
		@elseif(!empty($company->delivery_days) )
            
            <div class="form-check form-check-inline delivery_days">
            @if(in_array($key,$company->delivery_days))
               
               
                    <input name="delivery_days[]" class="form-check-input" type="checkbox" id="inlineCheckbox1" value="{{$key}}" checked>
                    <label class="form-check-label text-success" for="inlineCheckbox1">{{__($day)}}</label>
               
            @else
               
                    <input name="delivery_days[]" class="form-check-input" type="checkbox" id="inlineCheckbox1" value="{{$key}}">
                    <label class="form-check-label" for="inlineCheckbox1">{{__($day)}}</label>
               
            @endif
            </div>
            {{-- NEW FORMS--}}
        @else
			<div class="form-check form-check-inline">
				<input name="delivery_days[]" class="form-check-input" type="checkbox" id="inlineCheckbox1" value="{{$key}}">
				<label class="form-check-label" for="inlineCheckbox1">{{__($day)}}</label>
			</div>
		@endif
	@endforeach
</div>
