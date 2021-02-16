<thead >
<tr >
    @if($translation['needed'])
        <th style="width: 17%" scope="col">{{__('translation')}}
            <br> {{__('Product')}} {{__('name')}}</th>
    @else
    <th style="width: 17%" scope="col">{{__('Product')}} {{__('name')}}</th>
    @endif
    @if($is_preferred_language && $is_preferred_currency) {{--SHOW UNCHANGABLE DATA IF DIFFERENT LANGUAGE OR CURRENCY---}}
    <th style="width: 10%" scope="col">{{__('Product')}} {{__('code')}}</th>
    @endif
    
    @if($is_preferred_language || !$is_preferred_currency) {{--SHOW PRICES--}}
    <th  style="width: 8%"  scope="col">{{__('Price per')}} <br><small class="text-primary">{{__('kg / l')}} </small></th>
    @endif
    
    @if($is_preferred_language && $is_preferred_currency ) {{--SHOW UNCHANGABLE DATA IF DIFFERENT LANGUAGE OR CURRENCY---}}
    <th  style="width: 5%" scope="col">{{__('Stock level')}} <br><small class="text-primary">{{__('kg / l / pack')}}</small> </th>
    <th  style="width: 5%" scope="col">{{__('Low stock notification')}} <br><small class="text-primary">{{__('kg / l / pack')}}</small> </th>
    <th  style="width: 7%" scope="col">{{__('Add / remove Stock')}}  <br><small class="text-primary">{{__('kg / l / pack')}}</small> </th>
    @endif
    
    <th style="width: 15%" scope="col">{{__('Type / Brand')}}</th>
    
    
    @if($is_preferred_language && $is_preferred_currency) {{--SHOW UNCHANGABLE DATA IF DIFFERENT LANGUAGE OR CURRENCY---}}
    <th  style="width: 8%"  scope="col">{{__('Unit size')}} <br><small class="text-primary">{{__('kg / l')}} </small></th>
    @endif
    
    
    @if($is_preferred_language || !$is_preferred_currency) {{--SHOW PRICES--}}
    <th  style="width: 10%"  scope="col">{{__('Price per unit')}}</th>
    @endif
    
    
    <th style="width: 10%" scope="col">{{__('Additional info')}}</th>
    
    
    @if($is_preferred_language && $is_preferred_currency) {{--SHOW UNCHANGABLE DATA IF DIFFERENT LANGUAGE OR CURRENCY---}}
     <th style="width: 5%" scope="col">{{__('Unset')}}</th>
    @endif
</tr>
</thead>
