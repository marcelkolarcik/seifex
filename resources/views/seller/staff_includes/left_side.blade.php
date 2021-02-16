<div class="list-group">
        <a class="list-group-item list-group-item-action active mb-2" href="{{ url('/seller') }}"> {{__('Dashboard')}}</a>
       
        @if(isset($companies))
        
            @foreach($companies as $role   =>  $companies_)
                    @foreach($companies_ as $key => $company_)
                       {{-- @if($delegated  ==  'delegated')
                                <div class=" d-flex justify-content-between align-items-center list-group-item">
                                    <a class="" href="{{ url('/seller/company',$company_->id) }}">{{$company_->seller_company_name}}</a>
                                </div>
                        @else
                            <div class=" d-flex justify-content-between align-items-center list-group-item list-group-item disabled">
                                {{$company_->seller_company_name}}
                            </div>
                        @endif--}}
                        <div class=" d-flex justify-content-between align-items-center list-group-item">
                            <a class="" href="{{ url('/seller/company',$company_->id) }}">{{$company_->seller_company_name}}</a>
                        </div>
                            @if(count($companies_) == 1)
                                @if(isset($staff_duties['buyers']))
                                <a class="list-group-item d-flex justify-content-between align-items-center" href="{{ url('/buyers',$company_->id) }}"> {{__('Available Buyers')}}</a>
                                @endif
                                @if(isset($staff_duties['locations']))
                                <a class="list-group-item d-flex justify-content-between align-items-center" href="{{ url('/locations',$company_->id) }}">{{__('Delivery locations')}}</a>
                                 @endif
                                 @if(isset($staff_duties['prices']))
                                <a class="list-group-item d-flex justify-content-between align-items-center" href="{{ url('/prices',$company_->id) }}">{{__('Default prices')}} </a>
                                @endif
                            @endif
                    @endforeach
            @endforeach
            
        @elseif(isset($company))
        
        
            <div class=" d-flex justify-content-between align-items-center list-group-item">
                <a class="" href="{{ url('/seller/company',$company->id) }}">{{$company->seller_company_name}}</a>
            </div>
                                @if(isset($staff_duties['buyers']))
                                    <a class="list-group-item" href="{{ url('/buyers',$company->id) }}">{{__('Available Buyers')}}</a>
                                @endif
                                @if(isset($staff_duties['locations']))
                                    <a class="list-group-item" href="{{ url('/locations',$company->id) }}">{{__('Delivery locations')}}</a>
                                @endif
                                @if(isset($staff_duties['prices']))
                                    <a class="list-group-item" href="{{ url('/prices',$company->id) }}">{{__('Default prices')}}</a>
                                @endif
         @endif
    
</div>
