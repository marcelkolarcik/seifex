
<div class="col-md-6">
    @include('includes.feedback')
    @include('errors.list')
    
    @component('components.form_wrapper')
        @component('components.label_header')
            {{__('Company details')}}
        @endcomponent
            @include('includes.forms.buyer_company_details')
    @endcomponent
    
   @component('components.form_wrapper')
        @component('components.label_header')
            {{__('Owner details')}}
        @endcomponent
        @include('includes.forms.buyer_owner_details')
   @endcomponent
   
   @component('components.form_wrapper')
        @component('components.label_header_with_button')
            <span> {{__('Buyer details')}}</span>
            @slot('button')
                <label class="btn btn-sm btn-outline-light_green text-light same_as_owner" id="buyer" >{{__('Same as owner')}}</label>
            @endslot
        @endcomponent
        @include('includes.forms.buyer_details')
   @endcomponent
   
   @component('components.form_wrapper')
        @component('components.label_header_with_button')
            <span> {{__('Buyer accountant details')}}</span>
            @slot('button')
                <label class="btn btn-sm btn-outline-light_green text-light same_as_owner" id="buyer_accountant" >{{__('Same as owner')}}</label>
            @endslot
        @endcomponent
        @include('includes.forms.buyer_accountant_details')
   @endcomponent
   
   
</div>

<div class="col-md-6">
    @component('components.form_wrapper')
        @component('components.label_header')
            {{__('Location details')}}
        @endcomponent
        @include('includes.forms.locations')
        <br>
    @endcomponent
    
   @component('components.form_wrapper')
        @component('components.label_header')
            {{__('Address details')}}
        @endcomponent
        @include('includes.forms.address_details')
   @endcomponent
    
    @component('components.form_wrapper_no_flex')
        @component('components.label_header')
            {{__('Currencies')}}
        @endcomponent
        @include('includes.forms.currencies')
    @endcomponent
    
    @component('components.form_wrapper_no_flex')
        @component('components.label_header')
            {{__('Languages')}}
        @endcomponent
        @include('includes.forms.languages')
    @endcomponent
    
    <div class="form-group submit_button">
        {!! Form::submit($submitButtonText, ['class' => 'btn btn-success form-control']) !!}
    
    </div>

</div>

