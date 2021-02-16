<ul class="list-group">
@if(!App\BuyerCompany::where('buyer_id',Auth::guard('buyer')->user()->id)->first() && \Auth::guard('buyer')->user()->role == 'buyer_owner')
        <a class="list-group-item list-group-item-active" href="{{ url('/create_buyer_company') }}">{{__('Create Company')}}</a>
@else
       <li class="list-group-item list-group-item-action active mb-1" >
           <a class="text-light mb-2  "  href="{{ url('/buyer') }}">
               {{__('Dashboard')}}
           </a>
       </li>
    
        @if(isset($companies))
            @foreach($companies as $key => $b_company)
                {{--{{dd($companies)}}--}}
               
                    {{--STAFF--}}
                @if ($b_company->logged_in_staff != null && $b_company->logged_in_staff['undelegated_at'] == null)
                    
                    <a class="list-group-item list-group-item-action  mb-1
                     {{$b_company->id != session()->get('company_id') ?: " active"}}   "
                       href="{{ url('/buyer/company',$b_company->id) }}">
                        
                        {{$b_company->buyer_company_name}}
                        <small>
                            @if( $b_company->logged_in_staff['accepted_at'] != null )
                                <br>
                                {{ ucwords( explode('_',$b_company->logged_in_staff['role']) [1] )}} :
                                {{__( $b_company->logged_in_staff['staff_position'])}}
                            
                            @elseif($b_company->logged_in_staff['accepted_at'] == null )
                                <br>
                                <span class="list-group-item list-group-item-action bg-light_green pt-1 pb-1">
                                      {{__('Acept the job of ')}} {{ ucwords( explode('_',$b_company->logged_in_staff['role']) [1] )}} :
                                    {{__( $b_company->logged_in_staff['staff_position'])}}
                                </span>
                            @endif
                        </small>
                    </a>
                    {{--OWNER--}}
                    @else
                    <a class="list-group-item list-group-item-action  mb-1
                     {{$b_company->id != session()->get('company_id') ?: " active"}}   "
                       href="{{ url('/buyer/company',$b_company->id) }}">
                        
                        {{$b_company->buyer_company_name}}
                    </a>
                @endif
                
            
            @endforeach
        @endif
         @if(isset($company))
               
                @if(\Auth::guard('buyer')->user()->role !== 'buyer_accountant')
                    @if(isset($company->product_lists))
                        @foreach($company->product_lists as $department => $product_list)
                           
                                <li class="list-group-item mb-1">
                                <a class=""
                                   href={{ url('/department/'.str_replace(' ','_',$department)) }} >
                                    {{__(str_replace('_',' ',$department))}}
                                </a>
                                </li>
                           
                        @endforeach
                    @endif
                    
                    @if (\Auth::guard('buyer')->user()->can('manage_products', App\ProductList::class))
                        {{--@if(explode('/',$_SERVER['REQUEST_URI'])[1]  != 'product_list' )--}}
                            <a class="list-group-item bg-secondary text-light"  href={{ url('/product_list') }} >{{__('Add or Edit products')}}</a>
                        {{--@endif--}}
                    @endif
                @endif
            @endif
@endif

    

</ul>
