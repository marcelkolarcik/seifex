

<div class="card-header bg-secondary text-light">{{__('Your delegations :')}}</div>
<div class="card-body">
    @if(isset($companies) && sizeof($companies)  ==  1 )
        <ul class="list-group">
            @foreach($companies as $company)
                @if($company->logged_in_staff['undelegated_at'] == null)
                    
                    @if(  $company->logged_in_staff['scope'] != null)
                        @foreach(  $company->logged_in_staff['scope']  as $type    =>  $details)
                            {{$type}}
                            <hr>
                           
                            @if($type=='last_locations')
                                @foreach( $company->logged_in_staff['scope']['last_locations'] as $level    =>   $locations)
                                    {{$level}}:
                                    @foreach($locations as $location    => $l_array)
                                        {{$location}},
                                    @endforeach
                                @endforeach
                            
                            @elseif($type == 'base_locations')
                                @foreach( $company->logged_in_staff['scope']['base_locations'] as  $level    =>   $locations)
                                    {{$level}} :
                                    @foreach($locations as $location => $key)
                                        
                                        {{$location}},
                                    @endforeach
                                @endforeach <hr>
                            
                            @elseif($type == 'languages')
                                @if($company->logged_in_staff['scope']['languages'] != null)
                                    @foreach( $company->logged_in_staff['scope']['languages'] as  $key    =>   $language)
                                        {{$language}},
                                    @endforeach
                                @endif<hr>
                            @elseif($type == 'departments')
                                @if($company->logged_in_staff['scope']['departments'] != null)
                                @foreach( $company->logged_in_staff['scope']['departments'] as  $key    =>   $department)
                                    {{$department}},
                                @endforeach <hr>
                                @endif
                            @endif
                        @endforeach
                    @else
                        {{__('You have no scope yet!')}}
                    @endif
                    
                    @if($company->logged_in_staff['duties'] != null)
                            <hr>
                        {{__('Duties')}} <br>
                        @foreach($company->logged_in_staff['duties'][$company->id][$company->logged_in_staff['staff_id']][$company->logged_in_staff['role']] as $duty =>$key)
                            {{ $duty }} <br>
                        @endforeach
                    @else
                        {{__('You have no duties  yet!')}}
                    @endif
                @else
                    {{__('You were fired !')}}
                @endif
            
            @endforeach
            
            <hr>
        
        </ul>
    @else
        @foreach($companies as $company)
            @if($company->undelegated_at == null)
                <li class="list-group-item list-group-item-primary text-dark">
                    {{__('In :')}} {{$company->seller_company_name}}
                    {{__('as')}} {{$company->logged_in_staff['staff_position']}}
                    {{ explode(' ', str_replace('_',' ',$company->logged_in_staff['role']))[1]  }}
                    
                    {{__('since')}}
                    {{$company->logged_in_staff['delegated_at']}}
                </li>
                <br>
            @else
                <li class="list-group-item list-group-item-danger">
                    {{__('Out :')}} {{$company->seller_company_name}}
                    {{__(' as')}}
                    {{ explode(' ', str_replace('_',' ',$company->logged_in_staff['role']))[1]  }}
                    
                    {{__('since')}}
                    {{$company->logged_in_staff['undelegated_at']}}
                
                </li>
            @endif
        @endforeach
    @endif
</div>

