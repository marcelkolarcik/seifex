<label  class="small" for="seller_name">{{__('Seller')}} {{__('name')}}</label>
{{ Form::text('seller_name', null,['class' => 'form-control form-control-sm','required','id' =>  'seller_name']) }}

<label class="small"  for="seller_phone_number">{{__('Seller')}} {{__('phone number')}}</label>
{{ Form::text('seller_phone_number', null,['class' => 'form-control form-control-sm ','required','id' =>  'seller_phone_number']) }}

<label  class="small" for="seller_email">{{__('Seller')}} {{__('email')}}</label>
{{ Form::email('seller_email', null, ['class' => 'form-control form-control-sm','required','id' =>  'seller_email']) }}
@include('includes.staff_check.seller_check')
