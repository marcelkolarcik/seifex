
@if(isset($admin_types))
    {!! Form::Label('role', 'Duty for role:') !!}
    {!! Form::select('role', $admin_types,  null  , ['class' => 'form-control','placeholder'  =>  'Select role']) !!}
@else
    {!! Form::Label('role', 'Duty for role:') !!}
    {!! Form::text('role', $duty->role  , ['class' => 'form-control']) !!}
@endif
<div class="form-group">
    
    {!! Form::Label('duty_name', 'Duty name : (short)') !!}
    {!! Form::text('duty_name', null, ['class' => 'form-control','placeholder'  =>  'Duty  name']) !!}

</div>
<div class="form-group">
    
    {!! Form::Label('duty_description', 'Duty description : (can be longer)') !!}
    {!! Form::textArea('duty_description', null, ['class' => 'form-control','rows'=>3,'placeholder'  =>  'Duty description']) !!}

</div>

<div class="form-group">
    
    {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}

</div>
