<div class="card">
       
            @component('components.label_header_with_button_no_mb')
                {{$company->seller_company_name}}
                @slot('button')
                    @if(\Auth::guard('seller')->user()->role    ===     'seller_owner')
                            <a  href="{{ url('/edit_seller_company',$company->id) }}" class="text-light" title="Edit Company Settings">
                                {{__('edit')}}
                            </a> |
                        <a  href="{{ url('/staff/') }}" class="text-light">{{__('Staff')}} </a>
                    @endif
                @endslot
            @endcomponent
           
       
</div>

