{{--<label class="small" for="address">{{__('street address line')}} 1</label>--}}
{{ Form::text('address[line_1]', null,['class' => 'form-control form-control-sm mb-1 col-md-11','required','placeholder'=>__('street line 1')]) }}

{{--<label class="small" for="address">{{__('street address line')}} 2</label>--}}
{{ Form::text('address[line_2]', null,['class' => 'form-control form-control-sm mb-1  col-md-11','required','placeholder'=>__('street line 2') ]) }}

{{--<label class="small" for="address">{{__('street address line')}} 3</label>--}}
{{ Form::text('address[line_3]', null,['class' => 'form-control form-control-sm mb-1  col-md-11','placeholder'=>__('area')]) }}

{{--<label class="small" for="address">{{__('city')}}</label>--}}
{{ Form::text('address[city]', null,['class' => 'form-control form-control-sm mb-1  col-md-11','required','placeholder'=>__('city')]) }}

{{--<label class="small" for="address">{{__('post code')}}</label>--}}
{{ Form::text('address[postal_code]', null,['class' => 'form-control form-control-sm mb-3  col-md-11','required','placeholder'=>__('post code')]) }}
