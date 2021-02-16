<label  class="small" for="contact_person">{{__('Contact')}} {{__('person')}}</label>
{{ Form::text('contact_person', null,['class' => 'form-control form-control-sm','required']) }}

<label  class="small" for="phone_number">{{__('Contact')}} {{__('phone number')}}</label>
{{ Form::text('phone_number', null,['class' => 'form-control form-control-sm','required']) }}

<label class="small"  for="email">{{__('Contact')}} {{__('email')}}</label>
{{ Form::email('email', null, ['class' => 'form-control form-control-sm','required']) }}
