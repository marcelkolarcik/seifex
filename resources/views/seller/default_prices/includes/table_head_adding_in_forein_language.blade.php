<thead >
<tr >
  
        <th style="width: 17%" scope="col">
            @if($c_preferred_language != $language)
            {{__('translation')}}
            <br>
            @endif
            {{__('Product')}} {{__('name')}}
        
        </th>
   
    
  
   
    <th style="width: 10%" scope="col">{{__('Product')}} {{__('code')}}</th>
  
    
   
    <th  style="width: 8%"  scope="col">{{__('Price per')}} <br><small class="text-primary">{{__('kg / l')}} </small></th>
   
    
   
    <th  style="width: 5%" scope="col">{{__('Stock level')}} <br><small class="text-primary">{{__('kg / l / pack')}}</small> </th>
    <th  style="width: 5%" scope="col">{{__('Low stock notification')}} <br><small class="text-primary">{{__('kg / l / pack')}}</small> </th>
    <th  style="width: 7%" scope="col">{{__('Add / remove Stock')}}  <br><small class="text-primary">{{__('kg / l / pack')}}</small> </th>
   
    
    <th style="width: 15%" scope="col">
        @if($c_preferred_language != $language)
        {{__('translation')}}
    <br>
        @endif
        {{__('Type')}}/ {{__('brand')}}</th>
    
   
    <th  style="width: 8%"  scope="col">{{__('Unit size')}} <br><small class="text-primary">{{__('kg / l')}} </small></th>
 
    
    
 
    <th  style="width: 10%"  scope="col">{{__('Price per unit')}}</th>
  
    
    
    <th style="width: 10%" scope="col">
        @if($c_preferred_language != $language)
        {{__('translation')}}
        <br>
        @endif
        {{__('Additional')}} {{__('info')}}</th>
    
    
   
     <th style="width: 5%" scope="col">{{__('Unset')}}</th>
 
</tr>
</thead>
