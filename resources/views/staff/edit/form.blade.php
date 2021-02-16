<form  action="{{ URL::to('add_staff') }}"  method="post" enctype="multipart/form-data">
    @csrf
<div class="row">
    @include('staff.edit.'.$staff_role)
    {!! Form::text('delegation_id',$delegation_id,['readonly','class'=>'d-none']) !!}
    {!! Form::text('staff_role',$staff_role,['readonly','class'=>'d-none']) !!}
    {!! Form::text('email',$staff_details['email'],['readonly','class'=>'d-none']) !!}
    {!! Form::text('staff_position',$staff_details['position'],['readonly','class'=>'d-none']) !!}
    {!! Form::text('phone_number',$staff_details['phone_number'],['readonly','class'=>'d-none']) !!}
    {!! Form::text('staff_id',$staff_details['staff_id'],['readonly','class'=>'d-none']) !!}
    {!! Form::text('name',$staff_details['name'],['readonly','class'=>'d-none']) !!}
    
   
   
</div>
</form>
