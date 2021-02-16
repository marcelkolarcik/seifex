
<div class="col-md-6">
    @include('includes.feedback')
    @include('errors.list')
   
    @component('components.form_wrapper')
        @component('components.label_header')
            {{__('Company details')}}
        @endcomponent
            @include('includes.forms.seller_company_details')
    @endcomponent
    
    <div class="form-group img-thumbnail ">
        <label class="text-primary" >{{__('Location')}} {{__('details')}}</label>
        <br>
        @include('includes.forms.locations')
    </div>
    
    
    <div class="form-group img-thumbnail">
        <label class="text-primary" >{{__('Currencies')}}</label>
        <br>
        @include('includes.forms.currencies')
    </div>
    
    
    <div class="form-group img-thumbnail">
        <label class="text-primary" >{{__('Languages')}}</label>
        <br>
        @include('includes.forms.languages')
    </div>
    
    
    <div class="form-group img-thumbnail">
        <label class="text-primary" >{{__('Address')}} {{__('details')}}</label>
        <br>
        @include('includes.forms.address_details')
    </div>
    
    
    
    <div class="form-group img-thumbnail">
        <label class="text-primary"  for="payment_method">{{__('Payment frequency')}}</label>
        <small>{{__('How often do you want buyers to pay you ?')}} </small>
        {{ Form::select('payment_method', $payment_frequency,null,['class' => 'form-control form-control-sm','placeholder' => __('Payment frequency'),'required']) }}
    </div>
    
    
</div>

<div class="col-md-6">
    
    <div class="form-group img-thumbnail">
        <label class="text-primary" >{{__('Owner')}} {{__('details')}}</label><br>
        @include('includes.forms.seller_owner_details')
    </div>
    
    <div class="form-group img-thumbnail">
        <label class="text-primary" >{{__('Seller')}} {{__('details')}}</label>
        &nbsp;&nbsp;<label class="btn btn-sm btn-outline-secondary same_as_owner" id="seller" for="contact_person">Same as owner</label><br>
        @include('includes.forms.seller_details')
    </div>
   
    <div class="form-group img-thumbnail">
        <label class="text-primary" >{{__('Seller')}} {{__('accountant')}} {{__('details')}}</label> &nbsp;&nbsp;<label class="btn btn-sm btn-outline-secondary same_as_owner" id="seller_accountant" for="contact_person">Same as owner</label><br>
        @include('includes.forms.seller_accountant_details')
    </div>
    
    <div class="form-group img-thumbnail">
    <label class="text-primary" >{{__('Delivery person')}} {{__('details')}}</label> &nbsp;&nbsp;<label class="btn btn-sm btn-outline-secondary same_as_owner" id="seller_delivery" for="contact_person">Same as owner</label><br>
    @include('includes.forms.seller_delivery_details')
    </div>
    
    <div class="form-group img-thumbnail ">
        <label class="text-primary" >{{__('Delivery')}} {{__('details')}}</label><br>
        <label class="small" for="last_order_at">{{__('Last order at')}} <small class="text-primary">{{__('for next day delivery')}}</small> </label>
        {{ Form::select('last_order_at',$times,null,['class' => 'form-control form-control-sm','required'])}}
        
        <label  class="small" for="days">{{__('Your delivery days')}} </label><br>
        @include('includes.forms.delivery_days')
    </div>
    
    
            <div class="form-group submit_button">
        {!! Form::submit($submitButtonText, ['class' => 'btn btn-success form-control']) !!}
                
         </div>
        
        </div>
</div>
