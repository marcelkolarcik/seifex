{{--<label class="small"  for="buyer_owner_name">{{__('Owner')}} {{__('name')}}</label>--}}
{{ Form::text('buyer_owner_name', \Auth::guard('buyer')->user()->name,
['class' => 'form-control form-control-sm mb-1 col-md-11',
'required',
'readonly',
'id' =>  'buyer_owner_name',
'placeholder'=>__('Owner').' '. __('name')]) }}

{{--<label  class="small" for="buyer_owner_phone_number">{{__('Owner')}} {{__('phone number')}}</label>--}}
{{ Form::text('buyer_owner_phone_number', null,
['class' => 'form-control form-control-sm mb-1 col-md-11',
'required',
'id' =>  'buyer_owner_phone_number',
'placeholder'=>__('Owner').' '. __('phone number')]) }}

{{--<label  class="small" for="buyer_owner_email">{{__('Owner')}} {{__('email')}}</label>--}}
{{ Form::email('buyer_owner_email', \Auth::guard('buyer')->user()->email,
['class' => 'form-control form-control-sm mb-3 col-md-11',
'required',
'readonly',
'id' =>  'buyer_owner_email',
'placeholder'=>__('Owner').' '. __('email')]) }}
