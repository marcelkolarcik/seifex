{{--DEPARTMENTS--}}

    <div class="list-group list-group-horizontal-sm mt-2 mb-1 col-md-12" >
        <a class="list-group-item list-group-item-action pt-1 pb-1 bg-grey-500 text-light disabled " >{{__('Please select department !')}}</a>
    </div>
   
    @foreach($companies as $company_id  =>  $company)
        <div class="list-group list-group-horizontal-sm mt-2 mb-1 col-md-12" >
        @if($company->price_lists != null)
                    @if(sizeof($companies) > 1)
                    <a class="list-group-item list-group-item-action pt-1 pb-1 bg-primary text-light disabled " >{{$company->buyer_company_name}}</a>
                    @endif
            @foreach($company->price_lists as $department =>  $product_list)
                   
                        <button class="list-group-item list-group-item-action pt-1 pb-1 department "
                                    data-department    =   "{{ $department }}"
                                    data-buyer_company_id    =   "{{$company_id }}">
                                {{__(str_replace('_',' ',$department))}}
                           
                        </button>
            @endforeach
            
        {{--@else--}}
                {{--<a class="list-group-item list-group-item-action pt-1 pb-1 bg-warning text-grey-800 disabled " >--}}
                    {{--{{__('Looks like you have no products...')}}--}}
                {{--</a>--}}
        @endif
                </div>
    @endforeach

    
    {{--@if(sizeof($buyer_companies) === 1 && $num_of_departments === 1)--}}
        {{----}}
        {{--<p class="card-title ml-2">{{__('Your department.')}}</p>--}}
        {{--<div class=" mb-1 mt-1 ">--}}
            {{--<button class="btn btn-sm btn-outline-success text-dark " >--}}
                {{--{{__(str_replace('_',' ',$department))}}--}}
            {{--</button>--}}
        {{--</div>--}}
    {{--@else--}}
        {{--<p class="card-title ml-2">{{__('Please select department !')}}</p>--}}
        {{--<div class=" mb-1 mt-1">--}}
            {{--@foreach($buyer_companies as $company_name   =>  $company_id )--}}
                {{--<p class="card-title ml-2">{{$company_name}}</p>--}}
                {{--@foreach($company_id as $departments)--}}
                    {{--@foreach($departments as $department)--}}
                        {{--<button class="btn btn-sm btn-outline-success text-dark department"--}}

                                {{--data-department    =   "{{ $department }}"--}}
                                {{--data-buyer_company_id    =   "{{ array_key_first($company_id) }}">--}}
                            {{--{{__(str_replace('_',' ',$department))}}--}}
                        {{--</button>--}}
                    {{--@endforeach--}}
                {{--@endforeach--}}
            {{--@endforeach--}}
        {{--</div>--}}
    {{----}}
    {{--@endif--}}

