<div class="card-header  text-light bg-secondary mb-2">
        <ul class="nav nav-tabs ">
            @foreach($links as $link)
                <li class="nav-item  ">
                    <a href="/{{lcfirst($link)}}/{{session()->get('company_id')}}" class="nav-link text-light
                    {{ isset($active[$link]) ? $active[$link]: '' }}
                      {{ isset($active[$link]) ? 'text-dark' : '' }}">
                    
                    {{__(str_replace('_',' ',$link))}}</a>
                </li>
            @endforeach
           
           
           
        </ul>
    </div>

