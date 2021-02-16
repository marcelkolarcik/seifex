<label  class="small" for="seller_delivery_name">{{__('Delivery person')}} {{__('name')}}</label>
{{ Form::text('seller_delivery_name', null,['class' => 'form-control form-control-sm','required','id' =>  'seller_delivery_name']) }}

<label class="small"  for="seller_delivery_phone_number">{{__('Delivery person')}} {{__('phone number')}}</label>
{{ Form::text('seller_delivery_phone_number', null,['class' => 'form-control form-control-sm ','required','id' =>  'seller_delivery_phone_number']) }}

<label  class="small" for="seller_delivery_email">{{__('Delivery person')}} {{__('email')}}</label>
{{ Form::email('seller_delivery_email', null, ['class' => 'form-control form-control-sm','required','id' =>  'seller_delivery_email']) }}

