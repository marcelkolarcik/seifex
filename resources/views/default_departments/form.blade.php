
    <div class="form-group">

        {!! Form::Label('department', __('Default Department Name:')) !!}
        {!! Form::text('department', null, ['class' => 'form-control']) !!}

    </div>


    <div class="form-group">

        {!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}

    </div>
