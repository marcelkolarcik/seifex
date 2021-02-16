<form  action="{{ URL::to('add_staff') }}"  method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        @include('staff.create.'.$staff_role)
       
    </div>
</form>
