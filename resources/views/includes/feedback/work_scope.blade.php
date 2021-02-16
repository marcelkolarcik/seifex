@if(session()->has('work_scope') )
    
    @component ('components.main_header_red')
        {{session()->get('work_scope')}}
    @endcomponent


@endif
