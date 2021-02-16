<ul class="nav nav-tabs ">
    <li class="nav-item">
        <a class="nav-link  {{isset($admins_active) ? $admins_active : 'text-light' }}" href="/owner/admins">Admins</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{isset($create_admins_active) ? $create_admins_active : 'text-light' }}" href="/owner/create_admin">Create Admin</a>
    </li>
    <li class="nav-item " >
        <a class="nav-link  {{isset($create_admin_types_active) ? $create_admin_types_active : 'text-light' }}" href="/owner/create_admin_types">Create Admin Type</a>
    </li>
   
    <li class="nav-item ">
        <a class="nav-link  {{isset($create_admin_duties_active) ? $create_admin_duties_active : 'text-light' }}" href="/owner/create_admin_duty">Create Admin Duty</a>
    </li>
   
   
</ul>
