{!! Form::text('invoice_frequency',$info->invoice_freq,['class'=>'d-none']) !!}
{!! Form::text('delivery_location_id',$info->delivery_location_id,['class'=>'d-none']) !!}
{!! Form::submit(__('Save your changes'), ['name' => 'submitbutton' , 'class' => 'btn btn-success  form-control']) !!}
