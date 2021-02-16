

@if (session('message') == 1)
    <div class="alert alert-success">
        <h4>{{__('Well done !')}}</h4>
    </div>
@endif
@if(session()->has('no_price_list'))
    <div class="alert alert-danger">
        <h4>{{__('You have no prices for this department !')}}</h4>
    </div>
@endif

@if (session('message') == 2)
    <div class="alert alert-danger">
        <h4>{{__('Something went wrong !')}}</h4>
    </div>
@endif
@if (session('message') == 'no country')
    <div class="alert alert-danger">
        <h4>{{__('Please select country !')}}</h4>
    </div>
@endif
@if(session('expanded') == true)
    <div class="alert alert-success">
        <h4>{{__('Expanded !')}}</h4>
   
    @if(session('replaced_locations'))
        <h6>{{__('Your new location includes your previous locations for')}}  <mark> {{ __(str_replace('_',' ',session('dep')))  }} </mark> : <br>
       @foreach(session('replaced_locations') as $location)
                  <small>
                  {{\App\Services\StrReplace::dash(  $location['country'])}}
                   @if($location['county'] != '')  / {{\App\Services\StrReplace::dash(  $location['county'])}} @endif
                   @if($location['county_l4'] != '') / {{\App\Services\StrReplace::dash(  $location['county_l4'])}}  @endif
                  </small>
                     <br>
              
       @endforeach
        
        </h6>
    @endif
    </div>
@endif

@if(session()->has('no_translation') )
    
   @component ('components.main_header_red')
       {{session()->get('no_translation')}}
   @endcomponent
   
   
@endif
@if(session('duplicate') == true)
  
    <div class="alert alert-danger">
        <h6>{{__('You already deliver')}}   <mark> {{  __(str_replace('_',' ',session('dep'))) }}</mark>  {{__('to')}} :
            <mark>
           
                {{\App\Services\StrReplace::dash(  session('country'))}}
            @if((session('county')) != '')  /   {{\App\Services\StrReplace::dash(  session('county'))}} @endif
                @if((session('county_l4')) != '') /  {{\App\Services\StrReplace::dash(  session('county_l4'))}}  @endif </mark>
             
            
        </h6>
    </div>
@endif

@if(session('child_location') == true)
    
    <div class="alert alert-danger">
        <h6>
            {{__('You already deliver')}} <mark> {{  __(str_replace('_',' ',session('dep'))) }}</mark> {{__('to')}}  :
            <mark>  {{\App\Services\StrReplace::dash(  session('main_location'))}}</mark>
        </h6>
        
        
    </div>
@endif
{{-- Updating company details--}}
@if(session('form_updated') == true)
    
    <div class="alert alert-success">
        <h6>
            {{__('Your updates were successful !')}}
        </h6>
    
    
    </div>
@endif
@if(session('country_currency'))
    <div class="alert alert-light_green">
        <h6>
            {{session('country_currency')}}
        </h6>
        
    </div>
@endif
@if(session('staff_duty_updated') == true)
    
    <div class="alert alert-success">
        <h6>
            {{__('Your staff duty updates were successful !')}}
        </h6>
    
    
    </div>
@endif
@if(session('admin_duty_updated') == true)
    
    <div class="alert alert-success">
        <h6>
            {{__('Your admin duty updates were successful !')}}
        </h6>
    
    
    </div>
@endif
@if(session('no_sellers') == true)
    <div class="alert alert-danger">
        <h6>
            {{__('You have no sellers yet !')}}
        </h6>
    
    
    </div>
@endif
@if(session('no_country_selected') == true)
    <div class="alert alert-danger">
        <h6>
            {{__('You have no country selected !')}}
        </h6>
    
    
    </div>
@endif
@if(session('country_added'))
    <div class="alert alert-success">
        <h6>
            {{__('You have added  these countries :')}} <hr>
            
            @foreach(session('country_added') as $country)
             {{$country}}<br>
            @endforeach
        </h6>
    
    
    </div>
@endif
@if(session('country_removed'))
    <div class="alert alert-success">
        <h6>
            {{__('You have removed  these countries :')}} <hr>
            @foreach(session('country_removed') as $country)
                {{$country}}<br>
            @endforeach
        </h6>
    
    
    </div>
@endif
@if(session('request_sent'))
    <div class="alert alert-success">
        <h6>
            {{__('You have requested')}} {{session('request_sent')}}{{__(', thank you !')}}
            {{__('An email was sent to the email address provided, please click on the link in your email, to confirm your request !')}}
        </h6>
    </div>
@endif

@if(session('no_days'))
    <div class="alert alert-danger">
        <h4>{{session('no_days')}}</h4>
    </div>
@endif

@if(session('deleted_product_list'))
    <div class="alert alert-danger">
        <h4>{{session('deleted_product_list')}}</h4>
    </div>
@endif
@if(session('check'))
    <div class="alert alert-danger">
        <h4>{{__('Check')}}</h4>
    </div>
@endif
