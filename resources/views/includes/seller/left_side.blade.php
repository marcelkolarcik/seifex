<div class="list-group">
    @if(!App\SellerCompany::where('seller_id',Auth::guard('seller')->user()->id)->first() && Auth::guard('seller')->user()->role == 'seller_owner')
        <a class="list-group-item list-group-item-active" href="{{ url('/create_seller_company') }}">{{__('Create Company')}}</a>
    @else
        <a class="list-group-item list-group-item-action active mb-2" href="{{ url('seller') }}"> {{__('Dashboard')}}</a>
     
        @if(isset($companies))
            @foreach($companies as $key => $s_company)
                     
                            <a class="list-group-item list-group-item-action  mb-1
                            {{$s_company->id != session()->get('company_id') ?: " active"}}"
                               href="{{ url('/seller/company',$s_company->id) }}">{{$s_company->seller_company_name}}
                            <small>

                                @if ($s_company->logged_in_staff != null)

                                    @if( $s_company->logged_in_staff['accepted_at'] != null)
                                        <br>
                                        {{ ucwords( explode('_',$s_company->logged_in_staff['role']) [1] )}} :
                                        {{__( $s_company->logged_in_staff['staff_position'])}}
        
                                    @elseif($s_company->logged_in_staff['accepted_at'] == null)
                                        <br>
                                        <span class="list-group-item list-group-item-action bg-light_green pt-1 pb-1">
                                      {{__('Acept the job of ')}} {{ ucwords( explode('_',$s_company->logged_in_staff['role']) [1] )}} :
                                            {{__( $s_company->logged_in_staff['staff_position'])}}
                                </span>
        
                                    @endif
                                @endif
                                
                                {{--@if (property_exists($s_company, 'accepted_at'))--}}
                                    {{--@if( $s_company->accepted_at != null)--}}
                                        {{--<br>--}}
                                    {{--{{__( ucwords( explode('_',$s_company->role) [1] ))}}  : {{__( $s_company->position)}}--}}
                                   {{----}}
                                    {{--@elseif( $s_company->accepted_at == null)--}}
                                        {{--<br>--}}
                                    {{--<span class="list-group-item list-group-item-action bg-light_green pt-1 pb-1">--}}
                                          {{--{{__('Acept the job of ')}} {{ ucwords( explode('_',$s_company->role) [1] )}} : {{__( $s_company->position)}}--}}
                                    {{--</span>--}}
                                    {{----}}
                                    {{--@endif--}}
                                {{--@endif--}}
                            </small>
                            </a>
                      
            @endforeach
        @endif
        @if(isset($company))
                    @if (Auth::guard('seller')->user()->can('seller_coordinate_requests', App\ProductList::class))
                    <a class="list-group-item d-flex justify-content-between align-items-center mb-1" href="{{ url('/buyers',$company->id) }}">
                        {{__('Available Buyers')}}</a>
                    @endif
                    @if (Auth::guard('seller')->user()->can('see_delivery_locations', App\DeliveryLocation::class))
                    <a class="list-group-item d-flex justify-content-between align-items-center mb-1" href="{{ url('/delivery_locations') }}">
                        {{__('Delivery locations')}}</a>
                    @endif
                    @if (Auth::guard('seller')->user()->can('see_default_prices', App\DefaultPriceList::class))
                    <a class="list-group-item d-flex justify-content-between align-items-center mb-1" href="{{ url('/prices') }}">
                        {{__('Default prices')}} </a>
                    @endif
                    @if (Auth::guard('seller')->user()->can('edit_payment_frequency', App\Invoice::class) && Auth::guard('seller')->user()->role    ==  'seller_accountant')
                            <a class="list-group-item d-flex justify-content-between align-items-center mb-1" href="{{ url('/buyers_for_accountant',$company->id) }}">
                                {{__('Buyers')}}</a>
                    @endif
                    
                @endif
       
    @endif
</div>
