<ul class="nav nav-tabs ">
    <li class="nav-item">
        <a class="nav-link  {{isset($countries_active) ? $countries_active : 'text-light' }}" href="/owner/countries">Current countries</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{isset($add_country_active) ? $add_country_active : 'text-light' }}" href="/owner/add_country">Add country</a>
    </li>
    <li class="nav-item " >
        <a class="nav-link  {{isset($remove_country_active) ? $remove_country_active : 'text-light' }}" href="/owner/remove_country">Remove country</a>
    </li>
    <li class="nav-item " >
        <a class="nav-link  {{isset($new_requests_active) ? $new_requests_active : 'text-light' }}" href="/owner/new_requests">New Requests</a>
    </li>
    
</ul>
