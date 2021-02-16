@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
          
            <div class="col-md-12">
                <div class="card">
                    <!-- List group -->
                    <div class="list-group list-group-horizontal-sm" id="myList" role="tablist">
                        <a class="list-group-item list-group-item-action disabled pt-1 pb-1 bg-secondary text-light" data-toggle="list" href="#name" role="tab">
                            {{$company->buyer_company_name}}
                            
                            {{__('Employees')}}
                        </a>
                        <a class="staff_link list-group-item list-group-item-action active pt-1 pb-1" data-toggle="list" href="#buyer_buyer" role="tab">Buyers</a>
                        <a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#buyer_accountant" role="tab">Accounts</a>
                        <a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#previous" role="tab">Previous</a>
                    </div>
                    
                    <div class="card-body">
                    @if($company->staff)
                        <!-- Tab panes -->
                            <div class="tab-content">
          
                                @foreach($staff as $role    =>  $employees)
                                 
                                    <div class=" mb-3 tab-pane {{($role != 'seller_seller' && $role != 'buyer_buyer') ? '': 'active'}}"
                                         id="{{$role}}" role="tabpanel">
            
                                       
                                         @foreach($employees as $employee_hash  =>  $employee)
                                           
                                            @if(!isset($employee['undelegated_at']))
                                               
                                                @include('staff.includes.employee')
                                             @endif
                                             
                                        @endforeach
                                      
                                        <button class="add_staff list-group-item list-group-item-light_green pt-1 pb-1 mt-1"
                                                data-staff_role    =  "{{$role}}" >
                                            {{__('Add new team member')}}
                                        </button>
                                    </div>
                                 
                                @endforeach
                                {{--PREVIOUS STAFF--}}
                                    <div class=" mb-3 tab-pane }}"
                                         id="previous" role="tabpanel">
                                       
                                        @foreach($staff as $role    =>  $employees)
                                          
                                                @foreach($employees as $employee_hash  =>  $employee)
                                               
                                                    @if(isset($employee['undelegated_at']) )
                                                   
                                                        @include('staff.includes.previous')
                                                    @endif
                                                @endforeach
                                           
                                        @endforeach
                                       
                                    </div>
                                    {{--NEED TO CREATE EMPTY DIVS FOR ROLES THAT HAVE NO EMPLOYEES--}}
                                    <div class=" mb-3 tab-pane "
                                         id="buyer_buyer" role="tabpanel">
    
                                    </div>
                                    <div class=" mb-3 tab-pane "
                                         id="buyer_accountant" role="tabpanel">
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
                               href="/edit_buyer_company/{{session()->get('company_id')}}">
                                {{__('Edit managers - Company form')}}
                            </a>
                            <hr>
                            {{__('To add company staff, you can do it on this page, we will send email to
							your new staff to register with seifex.com and then
							 you will be able to assign duties and scope of work (locations and departments) to them.')}}
                            <div class="list-group list-group-horizontal-sm" id="myList" role="tablist">
                            <button class="add_staff list-group-item list-group-item-light_green pt-1 pb-1 mt-1 mr-1"
                                    data-staff_role    =  "buyer_buyer" >
                                {{__('Add new buyer staff')}}
                            </button>
                            <button class="add_staff list-group-item list-group-item-light_green pt-1 pb-1 mt-1"
                                    data-staff_role    =  "buyer_accountant" >
                                {{__('Add new accountant staff')}}
                            </button>
                            </div>
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
