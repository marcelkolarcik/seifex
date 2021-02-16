{{--<label class="small"  for="buyer_name">{{__('Buyer')}} {{__('name')}}</label>--}}
{{ Form::text('buyer_name', null,
['class' => 'form-control form-control-sm  mb-1 col-md-11',
'required',
'id' =>  'buyer_name',
'placeholder'=>__('Buyer').' '. __('name')]) }}

{{--<label class="small"  for="buyer_phone_number">{{__('Buyer')}} {{__('phone number')}}</label>--}}
{{ Form::text('buyer_phone_number', null,
['class' => 'form-control form-control-sm mb-1 col-md-11',
'required',
'id' =>  'buyer_phone_number',
'placeholder'=>__('Buyer').' '. __('phone number')]) }}

{{--<label class="small"  for="buyer_email">{{__('Buyer')}} {{__('email')}}</label>--}}
{{ Form::email('buyer_email', null,
['class' => 'form-control form-control-sm mb-3 col-md-11',
'required',
'id' =>  'buyer_email',
'placeholder'=>__('Buyer').' '. __('email')]) }}
@include('includes.staff_check.buyer_check')
