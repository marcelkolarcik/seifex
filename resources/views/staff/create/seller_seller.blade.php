<form  action="{{ URL::to('add_staff') }}"  method="post" enctype="multipart/form-data">
    @csrf
  
    
    <div class="row">
    
        <div id="details" class="col-md-4 p-0 m-0">
            @include('staff.includes.departments')
            @component('components.label_header_primary')
                {{__('Choose locations.')}}
            @endcomponent
            @include('staff.includes.locations')
        </div>
        <div  class="col-md-4 text-grey-800">
            @component('components.label_header_primary')
                {{__('Refine locations')}}
            @endcomponent
            <div id="new_staff_info">
             <p> {{__('Choose locations and departments new member will be working with. If no department will be selected, all departments will be assigned.')}}</p>
            </div>
            <div id="load" >
            </div>
        </div>
        <div class="col-md-4">
            @include('staff.create.contact_details',['team'=>'Sales'])
            <div class="included_locations d-none">
                @component('components.label_header_primary')
                    {{__('Included locations')}}
                @endcomponent
            </div>
            <div id="new_staff" class=" m-0 p-0 text-secondary" ></div>
            <div>
                @include('staff.includes.create_button')
            </div>
        </div>
    </div>
    
  
    
    
   
</form>


