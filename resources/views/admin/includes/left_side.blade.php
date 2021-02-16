<div class="list-group">
    <a class="list-group-item mb-1" href="{{ url('/admin') }}">{{__('Dashboard')}}</a>
    @if (\Auth::guard('admin')->user()->can('create_departments', App\DefaultDepartment::class))
    <a class="list-group-item mb-1" href="{{ url('/departments') }}">{{__('Departments')}}</a>
    @endif
    @if (\Auth::guard('admin')->user()->can('create_staff_duties', App\Duty::class))
    <a class="list-group-item mb-1" href="{{ url('/staff_duties') }}">{{__('Duties')}}</a>
    @endif
   
</div>
