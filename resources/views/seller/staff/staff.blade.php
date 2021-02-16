@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
           
            <div class="col-md-12">
                <div class="card">
                    <!-- List group -->
                    <div class="list-group list-group-horizontal-sm" id="myList" role="tablist">
                        <a class="list-group-item list-group-item-action  pt-1 pb-1 bg-secondary text-light"  title="{{__('Dashboard')}}" href="/seller">
                          {{session()->get('seller_company_name')}}
                        </a>
                        <a class="list-group-item list-group-item-action disabled pt-1 pb-1 bg-grey-500 text-light" data-toggle="list" href="#name" role="tab">   {{__('Team')}} </a>
                        <a class="staff_link list-group-item list-group-item-action active pt-1 pb-1" data-toggle="list" href="#seller_seller" role="tab">{{__('Sales')}}    </a>
                        <a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#seller_accountant" role="tab">{{__('Accounts')}}</a>
                        <a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#seller_delivery" role="tab">{{__('Delivery')}}</a>
                        <a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#previous" role="tab">{{__('Previous')}}</a>
                    </div>
                   
                    <div class="card-body">
                    @if(count($staff))
                        <!-- Tab panes -->
                            <div class="tab-content">
           
                                @foreach($staff as $role    =>  $employees)
                                   
                                    <div class=" mb-3 tab-pane {{($role != 'seller_seller' && $role != 'buyer_buyer') ? '': 'active'}}"
                                         id="{{$role}}" role="tabpanel">
                                        @if($loop->first)
                                            <div id="transfer_companies"></div>
                                        @endif
                                        <div class="list-group list-group-horizontal-sm mb-2" id="myList" role="tablist">
                                            <button class="add_staff list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1"
                                                    data-staff_role    =  "{{$role}}" >
                                                {{__('Add new staff')}}
                                            </button>
                                            <button class="list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1" >
                                                <a  href="/edit_seller_company/{{session()->get('company_id')}}" style="text-decoration: none" title="{{__('Edit managers - Company form')}}">
                                                    {{__('Edit managers - Company form')}}
                                                </a>
                                            </button>
                                            @if($role != 'seller_delivery')
                                            <button class="transfer_companies list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1"
                                            data-staff_role = "{{$role}}">
                                                {{__('Transfer companies')}}
                                            </button>
                                            @endif
                                        </div>
                                        
                                        @foreach($employees as $employee_hash  =>  $employee)
                                          
                                            {{--@if(!isset($employee['undelegated_at']) == null)--}}
                                                {{--{{dd($employee['undelegated_at'] == null)}}--}}
                                                {{--@endif--}}
                                          {{--{{dd($employees)}}--}}
                                                {{--@if($employee['undelegated_at'] != null )--}}
                                                    {{--@include('staff.includes.employee')--}}
                                                {{--@endif--}}
                                            
                                            
                                            @if(!isset($employee['undelegated_at']) )
                                                @include('staff.includes.employee')
                                            @endif
                    
                                        @endforeach
                                        
                                    </div>
                                  
                                @endforeach
                                {{--PREVIOUS STAFF--}}
                                <div class=" mb-3 tab-pane }}"
                                     id="previous" role="tabpanel">
                
                                    @foreach($staff as $role    =>  $employees)
                                      
                                            @foreach($employees as $employee_hash  =>  $employee)
                                                @if($employee['undelegated_at'] != null )
                                                    @include('staff.includes.previous')
                                                @endif
                                            @endforeach
                                     
                                    @endforeach
                                </div>
                                {{--NEED TO CREATE EMPTY DIVS FOR ROLES THAT HAVE NO EMPLOYEES--}}
                                <div class=" mb-3 tab-pane "
                                     id="seller_seller" role="tabpanel">
                                    <div class="list-group list-group-horizontal-sm mb-2" id="myList" role="tablist">
                                    <button class="add_staff list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1"
                                            data-staff_role    =  "seller_seller" >
                                        {{__('Add new staff')}}
                                    </button>
                                    <button class="list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1" >
                                        <a  href="/edit_seller_company/{{session()->get('company_id')}}" style="text-decoration: none" title="{{__('Edit managers - Company form')}}">
                                            {{__('Edit managers - Company form')}}
                                        </a>
                                    </button>
                                   
                                    </div>
                                </div>
                                <div class=" mb-3 tab-pane "
                                     id="seller_accountant" role="tabpanel">
                                    <div class="list-group list-group-horizontal-sm mb-2" id="myList" role="tablist">
                                    <button class="add_staff list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1"
                                            data-staff_role    =  "seller_accountant" >
                                        {{__('Add new staff')}}
                                    </button>
                                    <button class="list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1" >
                                        <a  href="/edit_seller_company/{{session()->get('company_id')}}" style="text-decoration: none" title="{{__('Edit managers - Company form')}}">
                                            {{__('Edit managers - Company form')}}
                                        </a>
                                    </button>
                                    
                                    </div>
                                </div>
                                    <div class=" mb-3 tab-pane "
                                         id="seller_delivery" role="tabpanel">
                                        <div class="list-group list-group-horizontal-sm mb-2" id="myList" role="tablist">
                                        <button class="add_staff list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1"
                                                data-staff_role    =  "seller_delivery" >
                                            {{__('Add new staff')}}
                                        </button>
                                        <button class="list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1" >
                                            <a  href="/edit_seller_company/{{session()->get('company_id')}}" style="text-decoration: none" title="{{__('Edit managers - Company form')}}">
                                                {{__('Edit managers - Company form')}}
                                            </a>
                                        </button>
                                       
                                        </div>
                                       
                                    </div>
                                <div class=" mb-3 tab-pane "
                                     id="previous" role="tabpanel">
                                </div>
        
        
        
                            </div>
    
    
                        @else
                       {{__('You have no staff yet !')}}
                            <hr>
                        {{__('To add or edit company managers, simply change contact details in your company form, and we will send email to
                        your new manager to register with seifex.com. Then you will be able to assign duties to him.')}}
                        
                           
                            <a class="list-group-item  pt-1 pb-1 bg-primary text-light"  title="{{__('Dashboard')}}"
                               href="/edit_seller_company/{{session()->get('company_id')}}">
                               {{__('Company form')}}
                            </a>
                            <hr>
                        {{__('Later , if you want to add company staff, you will have option to do it on this page,
                         you will be able to assign duties and scope of work (locations and departments) to your new staff.')}}
                    @endif
                    <div id="duties_form"></div>
                    <div id="new_staff_form"></div>
                    <div id="scope_form"></div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
