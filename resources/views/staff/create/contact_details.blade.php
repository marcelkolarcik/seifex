<div class="row ">
    @component('components.label_header_primary')
        {{__('New '.$team.' team member summary.')}}
    @endcomponent
  @if($staff_position !== 'manager')
    <div class="input-group input  mb-2">
        <div class="input-group-prepend input-group-sm" >
            <div class=" form-control form-control-sm input-group-text bg-primary text-light">
                {{__('Name')}}
            </div>
        </div>
        {{ Form::text('name', null,
		['class' => 'form-control form-control-sm ',
		'required',
		'placeholder'=>__('Name')]) }}
    </div>
        <div class="input-group input  mb-2">
            <div class="input-group-prepend input-group-sm" >
                <div class=" form-control form-control-sm input-group-text bg-primary text-light">
                    {{__('Phone Number')}}
                </div>
            </div>
            {{ Form::text('phone_number', null,
        ['class' => 'form-control form-control-sm ',
        'required',
        'placeholder'=>__('Phone Number')]) }}
        </div>
    <div class="input-group input mb-2">
        <div class="input-group-prepend input-group-sm" >
            <div class=" form-control form-control-sm input-group-text bg-primary text-light">
                {{__('Email')}}
            </div>
        </div>
        {{ Form::text('email', null,
        ['class' => 'form-control form-control-sm ',
        'required',
        'placeholder'=>__('Email')]) }}
    </div>
   @else
           {{--FOR MANAGER--}}
            {!! Form::text('phone_number',$staff_phone_number,['readonly','class'=>'d-none']) !!}
            {!! Form::text('manager_delegation_id',$delegation_id,['readonly','class'=>'d-none']) !!}
    @endif
        @include('staff.includes.languages')
        {!! Form::text('staff_position',$staff_position,['readonly','class'=>'d-none']) !!}
        {!! Form::text('staff_id',$staff_id,['readonly','class'=>'d-none']) !!}
</div>
