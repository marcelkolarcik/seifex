
<div class="list-group list-group-horizontal-sm {{$employee['staff_position'] == 'staff' ?' mb-1':' mb-2'}}" >
        @if(/*$single->staff_position == 'staff' && */$employee['accepted_at'] != null)
        <button class="list-group-item  pt-1 pb-1 bg-light_green scope"
           role="tab"  {{-- href="/staff/{{$single->id}}/{{$company_id}}"--}}
           title="{{__('Scope of work for')}} {{ $employee['staff_name']}}"
           data-staff_id                =  "{{ $employee['staff_id']}}"
           data-staff_position          =  "{{$employee['staff_position']}}"
           data-role                    =  "{{$employee['role']}}"
           data-delegation_id           =  "{{$employee['delegation_id']}}"
           data-phone_number            =  "{{$employee['phone_number']}}"
           data-work_scopes_staff_id    =  "{{$employee['staff_id']}}"
           data-staff_hash              =  "{{$employee_hash}}"
          
           
        >
            {{__('scope')}}
        </button>
        @endif
   
        <a class="list-group-item list-group-item-action pt-1 pb-1"  >{{__($employee['staff_position'])}}  </a>
            <a class="list-group-item list-group-item-action pt-1 pb-1"  >{{$employee['staff_name']}}</a>
        <a class="list-group-item list-group-item-action pt-1 pb-1"  >  {{$employee['email']}} {{$employee['staff_id']}}</a>
        <a class="list-group-item list-group-item-action pt-1 pb-1 " >
    
    {{--dd MMM/D/YY HH:mm--}}
            {{$employee['phone_number']}}
            
       </a>
     
        @if($employee['accepted_at'] == null)
        
        <a class="list-group-item list-group-item pt-1 pb-1 bg-warning" >
                {{__('waiting...')}}
        </a>
        @else
          
            <button class="list-group-item list-group-item pt-1 pb-1 bg-light_green duties"
               role="link"  {{-- href="/staff/{{$single->id}}/{{$company_id}}"--}}
               title="Duties for {{$employee['staff_name']}}"
               data-staff_id            =  "{{$employee['staff_id']}}"
               data-role            =  "{{$employee['role']}}"
               data-staff_hash      =  "{{$employee_hash}}"
                >
               {{__('Duties')}}
            </button>
       
            <button class="list-group-item  bg-danger text-light pt-1 pb-1 undelegate_staff"
               title="{{__('Fire this employee ?')}}"
               data-text  = "{{__('You will not be able to reverse this process !')}}"
               data-staff_role  = "{{$employee['role']}}"
               data-staff_email =  "{{$employee['email']}}"
               data-staff_position  =   "{{$employee['staff_position']}}"
               data-company_name    ="{{ $employee['delegator_company_name'] }}"
               data-company_name_desc    =  "{{strtok($employee['role'],'_') == 'seller' ? 'seller_company_name' : 'buyer_company_name'}}"
               data-wrong  = "{{__('Something went wrong !')}}"
               data-later =  "{{__('Please try again later.')}}"
               role="tab">
                {{__('fire')}}
            </button>
        @endif
      
    </div>

    
   


