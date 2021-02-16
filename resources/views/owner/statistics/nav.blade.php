<ul class="nav nav-tabs ">
    <li class="nav-item">
        <a class="nav-link  {{isset($countries_active) ? $countries_active : 'text-light' }}" href="/owner/statistics">{{$page}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{isset($type_active)&&$type_active=='buyer_active' ? 'active' : 'text-light' }}" href="/owner/statistics/buyer">By buyers</a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{isset($type_active)&&$type_active=='seller_active' ? 'active' : 'text-light' }}" href="/owner/statistics/seller">By sellers</a>
    </li>
    @if(isset($country))
        <li class="nav-item">
            <a class="nav-link  {{isset($country_active) ? $country_active : 'text-light' }}" href="/owner/statistics/{{$type}}/{{$country_id}}">Country : {{$country}}</a>
        </li>
    @endif
    @if(isset($company))
        <li class="nav-item">
            <a class="nav-link  {{isset($company_active) ? $company_active : 'text-light' }}" href="/owner/statistics/{{$type}}/{{$company_id}}">Company : {{$company}}</a>
        </li>
    @endif
    {{--<li class="nav-item">--}}
        {{--<a class="nav-link {{isset($add_country_active) ? $add_country_active : 'text-light' }}" href="/owner/add_country">Add country</a>--}}
    {{--</li>--}}
    {{--<li class="nav-item " >--}}
        {{--<a class="nav-link  {{isset($remove_country_active) ? $remove_country_active : 'text-light' }}" href="/owner/remove_country">Remove country</a>--}}
    {{--</li>--}}
    {{--<li class="nav-item " >--}}
        {{--<a class="nav-link  {{isset($new_requests_active) ? $new_requests_active : 'text-light' }}" href="/owner/new_requests">New Requests</a>--}}
    {{--</li>--}}

</ul>
