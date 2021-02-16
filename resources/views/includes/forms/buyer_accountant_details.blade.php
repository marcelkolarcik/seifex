{{--<label class="small" for="buyer_accountant_name">{{__('Buyer')}} {{__('accountant')}} {{__('name')}}</label>--}}
{{--{{ Form::text('buyer_accountant_name', null,['class' => 'form-control form-control-sm','required','id' =>  'buyer_accountant_name']) }}--}}

{{--<label  class="small" for="buyer_accountant_phone_number">{{__('Buyer')}} {{__('accountant')}} {{__('phone number')}}</label>--}}
{{--{{ Form::text('buyer_accountant_phone_number', null,['class' => 'form-control form-control-sm','required','id' =>  'buyer_accountant_phone_number']) }}--}}

{{--<label  class="small" for="buyer_accountant_email">{{__('Buyer')}} {{__('accountant')}} {{__('email')}}</label>--}}
{{--{{ Form::email('buyer_accountant_email', null, ['class' => 'form-control form-control-sm','required','id' =>  'buyer_accountant_email']) }}--}}
{{--@include('includes.staff_check.buyer_accountant_check')--}}

{{ Form::text('buyer_accountant_name', null,
['class' => 'form-control form-control-sm  mb-1 col-md-11',
'required',
'id' =>  'buyer_accountant_name',
'placeholder'=>__('Buyer').' '.__('accountant').' '. __('name')]) }}

{{ Form::text('buyer_accountant_phone_number', null,
['class' => 'form-control form-control-sm  mb-1 col-md-11',
'required',
'id' =>  'buyer_accountant_phone_number',
'placeholder'=>__('Buyer').' '.__('accountant').' '. __('phone number')]) }}

{{ Form::text('buyer_accountant_email', null,
['class' => 'form-control form-control-sm  mb-3 col-md-11',
'required',
'id' =>  'buyer_accountant_email',
'placeholder'=>__('Buyer').' '.__('accountant').' '. __('email')]) }}
@include('includes.staff_check.buyer_accountant_check')
