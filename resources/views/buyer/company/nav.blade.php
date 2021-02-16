<div class="card">
        @component('components.label_header_with_button_no_mb')
            {{$company->buyer_company_name}}
            @slot('button')
                @if(\Auth::guard('buyer')->user()->role    ===     'buyer_owner')
                    <a  href="{{ url('/edit_buyer_company',$company->id) }}" class="text-light" title="Edit Company Settings">
                        {{__('edit')}}
                    </a> |
                    <a  href="{{ url('/staff/') }}" class="text-light">{{__('Staff')}} </a>
                @endif
            @endslot
        @endcomponent
</div>

