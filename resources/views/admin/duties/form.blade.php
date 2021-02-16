
    <div class="form-group">
    @if(isset($roles))
        {!! Form::Label('role', __('Duty for role:')    ) !!}
        {!! Form::select('role', $roles,  null  , ['class' => 'form-control','placeholder'  =>  __('Select role')]) !!}
    @else
            {!! Form::Label('role', __('Duty for role:')) !!}
            {!! Form::text('role', $duty->role  , ['class' => 'form-control']) !!}
    @endif
    </div>
    <div class="form-group">
    
        {!! Form::Label('duty_for', __('Duty for') ) !!}
        {!! Form::text('duty_for', null, ['class' => 'form-control','placeholder'  =>  __('Action')  ]) !!}

    </div>
    <div class="form-group">
    
        {!! Form::Label('duty_name', __('Duty name : (short)') ) !!}
        {!! Form::text('duty_name', null, ['class' => 'form-control','placeholder'  =>  __('Duty name')  ]) !!}

    </div>

    <div class="form-group">
    
        {!! Form::Label('lead_duty', __('Is it lead duty ?') ) !!} <br>
      {{--  {!! Form::input('lead_duty', null, ['type'=>'radio','class' => 'form-control','placeholder'  =>  __('Yes')  ]) !!}--}}
        {!! Form::Label('lead_duty', __('Yes') ) !!}
        {!! Form::radio('lead_duty', '1', true)!!}<br>
        {!! Form::Label('lead_duty', __('No') ) !!}
        {!! Form::radio('lead_duty', '0')!!}
       {{-- <input name="payment_frequency" class="form-check-input  payment_frequency" type="radio"  value="" checked>
        <label class="form-check-label text-success" >{{__('Yes')}}</label>
    
        <input name="payment_frequency" class="form-check-input  payment_frequency" type="radio"  value="" >
        <label class="form-check-label text-success" >{{__('No')}}</label>--}}

    </div>
    <div class="form-group">
    
        {!! Form::Label('duty_description', __('Duty description : (can be longer)')    ) !!}
        {!! Form::textArea('duty_description', null, ['class' => 'form-control','rows'=>3,'placeholder'  =>  __('Duty description')]) !!}

    </div>

    <div class="form-group">

        {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}

    </div>
