<div class="list-group">
        <a class="list-group-item list-group-item-action active mb-2" href="{{ url('/owner') }}">
            Dashboard
        </a>
        <a class="list-group-item mb-1" href="{{ url('/owner/admins') }}">
            Admins
        </a>
    <a class="list-group-item mb-1" href="{{  url('/owner/roles') }}">
        Roles
    </a>
    <a class="list-group-item mb-1" href="{{  url('/departments') }}">
        Departments
    </a>
    <a class="list-group-item mb-1" href="{{  url('staff_duties') }}">
        Staff duties
    </a>
    <a class="list-group-item mb-1" href="{{url('/owner/countries') }}">
        Countries
    </a>
    <a class="list-group-item mb-1" href="{{url('/owner/income') }}">
        Seifex Income
    </a>
    <a class="list-group-item mb-1" href="{{url('/owner/statistics') }}">
       Statistics
    </a>
    <a class="list-group-item mb-1" href="{{url('#') }}">
        Sellers
    </a>
    <a class="list-group-item mb-1" href="{{url('/owner/backup') }}">
        BACKUP
    </a>
    <a class="list-group-item mb-1" href="{{url('/log-viewer') }}">
        LOG VIEWER
    </a>
   {{-- <a class="list-group-item mb-2" href="{{ url('/owner/create_admin_types') }}">
        Create Admin Types
    </a>
    <a class="list-group-item mb-2" href="{{ url('/owner/create_admin') }}">
       Create Admin
    </a>--}}
</div>
