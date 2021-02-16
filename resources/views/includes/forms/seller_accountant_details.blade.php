<label class="small" for="seller_accountant_name">{{__('Seller')}} {{__('accountant')}} {{__('name')}}</label>
{{ Form::text('seller_accountant_name', null,['class' => 'form-control form-control-sm','required','id' =>  'seller_accountant_name']) }}

<label class="small"  for="seller_accountant_phone_number">{{__('Seller')}} {{__('accountant')}} {{__('phone number')}}</label>
{{ Form::text('seller_accountant_phone_number', null,['class' => 'form-control form-control-sm','required','id' =>  'seller_accountant_phone_number']) }}

<label class="small"  for="seller_accountant_email">{{__('Seller')}} {{__('accountant')}} {{__('email')}}</label>
{{ Form::email('seller_accountant_email', null, ['class' => 'form-control form-control-sm','required','id' =>  'seller_accountant_email']) }}
@include('includes.staff_check.seller_accountant_check')
