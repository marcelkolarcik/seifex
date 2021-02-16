<label  class="small" for="seller_owner_name">{{__('Owner')}} {{__('name')}}</label>
{{ Form::text('seller_owner_name', \Auth::guard('seller')->user()->name,['class' => 'form-control form-control-sm','required','readonly','id' =>  'seller_owner_name']) }}

<label  class="small" for="seller_owner_phone_number">{{__('Owner')}} {{__('phone number')}}</label>
{{ Form::text('seller_owner_phone_number', null,['class' => 'form-control form-control-sm','required','id' =>  'seller_owner_phone_number']) }}

<label  class="small" for="seller_owner_email">{{__('Owner')}} {{__('email')}}</label>
{{ Form::email('seller_owner_email', \Auth::guard('seller')->user()->email, ['class' => 'form-control form-control-sm','required','readonly','id' =>  'seller_owner_email']) }}
