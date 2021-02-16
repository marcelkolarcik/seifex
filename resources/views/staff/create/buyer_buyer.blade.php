

<form  action="{{ URL::to('add_staff') }}"  method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6">
            @include('staff.create.contact_details',['team'=>'Buyers'])
        </div>
        <div class="col-md-6">
            @include('staff.includes.departments')
        </div>
    </div>
    @include('staff.includes.create_button')
</form>
