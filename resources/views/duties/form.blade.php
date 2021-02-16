
    {!!  Form::open(['method' => 'POST','action' => [ 'StaffController@update_duties']])!!}
    {{ csrf_field() }}

   {{-- staff_dutiesduties)}}--}}
  
    <div class="card list-group list-group-horizontal-sm" >
        <a class="list-group-item list-group-item-action bg-secondary text-light pt-1 pb-1"  role="tab">{{__( ucwords($staff['staff_position']))}} : </a>
        <a class="list-group-item list-group-item-action bg-secondary text-light pt-1 pb-1"  role="tab">    {{$staff['staff_name']}}</a>
        <a class="list-group-item list-group-item-action pt-1 pb-1"  role="tab">  </a>
    </div>
    @foreach($duties as $action => $action_duties)
        <div class="card mb-1 border-info">
            <div class="card-header border-light_green bg-light_green text-dark">
                {{__('Action : ')}} <b>{{$action}}</b>
            </div>
            <div class="card-body">
                @foreach($action_duties as $duty_name   =>  $duty_data)
                    @if($duty_data['lead_duty'] !== 1)
                        <div class="ml-5">
                    @endif
    
                            <div class="list-group-item {{$duty_data['active'] === 1 ? 'text-primary':'text-black-50' }}  mb-1 border-0">
                                    <input name="duties[]"
                                       class="form-check-input form-check duty {{ hash('adler32',$duty_data['duty_for']) }}
                                       {{   $duty_data['lead_duty'] === 0 ?  hash('adler32',$duty_data['duty_for']).'-sub' :  hash('adler32',$duty_data['duty_for']).'-lead'  }}"
                                       data-box_disabled="{{isset($duty_data['disabled'])  ? 'yes':'no' }}"
                                       type="checkbox"
                                       value="{{$duty_name}}"
                                       {{$duty_data['active'] === 1 ? 'checked':'' }}
                                       data-lead=  "{{$duty_data['lead_duty']}}"
                                       data-duty_for=  "{{hash('adler32',$duty_data['duty_for'])}}"
                                   {{-- {{isset($duty_data['disabled'])  ? 'disabled':'' }}--}}
                                >  <i class="fas fa-hand-point-left fa-lg fal
                            {{ $duty_data['lead_duty'] === 0 ? '' :   hash('adler32',$duty_data['duty_for']).'-lead-desc_icon' }} d-none text-danger"></i>
                                <label class="form-check-label text-capitalize
                                    {{hash('adler32',$duty_data['duty_for'])}}
                                {{   $duty_data['lead_duty'] === 0 ? '' :  hash('adler32',$duty_data['duty_for']).'-lead-desc'  }}"
                                >{{$duty_data['duty_description']}}</label>
                            </div>
                            
                    @if($duty_data['lead_duty'] !== 1)
                        </div>
                    @endif
                    
                @endforeach
            </div>
        </div>
    @endforeach
  

<hr>
        {!! Form::text('staff_role',$staff['role'], ['class' => 'd-none','readonly']) !!}
        {!! Form::text('staff_id',$staff['staff_id'], ['class' => 'd-none','readonly']) !!}
        {!! Form::text('staff_hash',$staff_hash, ['class' => 'd-none','readonly']) !!}

<div class="form-group submit_button">
    {!! Form::submit($submitButtonText, ['class' => 'btn btn-light_green btn-sm form-control']) !!}
</div>

</form>
