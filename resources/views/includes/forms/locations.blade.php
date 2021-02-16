<?php
$country = null;
if (isset($search_country)) {
    $country = $search_country;
}
elseif (isset($company->country)) {
    
    $country = $company->country;
}


?>

<div class="col-md-12">
   
    {{-- RESETIN COUNTRY WHEN CREATING COMPANY TO ALLOW USER TO
    REPOPULATE CURRENCIES AND LANGUAGES BY RESELECTING THE COUNTRY AGAIN--}}
    @if((!empty($errors))  && isset($creating_company_class) && $creating_company_class != '')
        
        <label for="country">
        @component('components.warning_header')
            {{__('Select the country')}}
        @endcomponent
        </label>
            <br>
        <select required="required"
                class="form-control form-control-sm"
                name="country"
                wrong="{{__('Something went wrong.')}}"
                later="{{__('Please try again later.')}}"
                id="country"
                required="required"
                placeholder="{{__('Country')}}">
            <option></option>
            @foreach ($country_levels as $key => $val)
                <option value="{{ $key }}">{{ $val }}</option>
            @endforeach
        </select>
    @else
        
       
    
        <label for="country">{{__('Country')}}</label>
        <br>
        <small>
            {{!isset($location_path) ? '': $location_path}}
        </small>
    
    {!! Form::select('country', $country_levels, isset($location_path) ? $country: null  ,[
	'wrong' =>__('Something went wrong.'),
	'later' =>__('Please try again later.'),
	'id' => 'country',
	'class' => 'form-control form-control-sm',
	'required' => 'required',
	'placeholder' => __('Country')]) !!}
    @endif
    
    <div class="form-group county d-none ">
        <label for="county">{{__('County')}}</label>
        {!! Form::select('county', $county_levels, null  ,[
        'id' => 'county',
        'class' => 'form-control form-control-sm',
        'placeholder' => __('County'),
        'wrong' =>__('Something went wrong.'),
        'later' =>__('Please try again later.'),]) !!}
    </div>
    <div class="form-group county_level_4 d-none">
        <label for="county_level_4">{{__('Location')}}</label>
        {!! Form::select('county_l4', $county_levels_4,null,[
        'id' => 'county_level_4',
        'class' => 'form-control form-control-sm',
        'placeholder' => __('Location'),
        'wrong' =>__('Something went wrong.'),
        'later' =>__('Please try again later.')]) !!}
    
    </div>
    
    <div id="old_counties" class="d-none">
        @if($country != null )
            @if(isset($company->county))  {!! Form::text('county',$company->county) !!} @endif
            @if(isset($company->county_l4))  {!! Form::text('county_l4',$company->county_l4) !!} @endif
        @endif
    </div>
</div>

