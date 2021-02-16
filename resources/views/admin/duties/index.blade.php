@extends('admin.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
            @include('admin.includes.left_side')
            </div>
            <div class="col-md-9 ">
                <div class="card mb-2">
                    <div class="card-header bg-secondary text-light d-flex justify-content-between align-items-center">
                                 {{__('Default Duties')}}
                        <a href="/create_duty" class="btn btn-primary btn-sm">{{__('Create new duty')}}</a>
                    </div>

                    <div class="card-body">
                      
                       @foreach($duties as $role    =>  $actions)
                           
                           <div class="card border-secondary">
                                <div class="card-header bg-secondary text-light" id="{{$role}}">
                                  {{__('Duties for :')}}
                                    @foreach(array_keys($duties) as $role_)
                                        @if($role === $role_)
                                        <a href="#{{$role_}}" class="card-link text-light" style="font-size: 1.6em">{{$role_}}</a>
                                        @else
                                         <a href="#{{$role_}}" class="card-link text-light_green">{{$role_}}</a>
                                        @endif
                                       
                                     @endforeach
                                   
                                </div>
                               <div class="card-body border-info">
                                   @foreach($actions as $action => $role_duties)
                                       <div class="card mb-1 border-info">
                                           <div class="card-header border-info text-dark bg-light_green">
                                               {{__('Action : ')}} <b>{{$action}}</b>
                                           </div>
                                           <div class="card-body">
                                               @foreach($role_duties as $duty)
                                                  <div class="img-thumbnail bg-transparent border-secondary text-secondary mb-2">
                                                       <div class="d-flex justify-content-between align-items-center">
                                                           <p class="card-text ">{{__('Name :')}} {{$duty['duty_name']}}</p>
                                                           <p class="card-text">{{__('Action :')}} {{$duty['duty_for']}}</p>
                                                           <p class="card-text">{{__('Lead duty :')}}
                                                               {{$duty['lead_duty'] === 1 ? __('Yes'):__('No') }}
                                                           </p>
                                                           <a href="/edit_duty/{{$duty['id']}}">{{__('Edit')}}</a>
                                                       </div>
                                                       <div>
                                                           <p class="card-text">{{__('Description :')}} <br>{{$duty['duty_description']}}</p>
                                                       </div>
                                                  </div>
                                                  
                                               @endforeach
                                           </div>
                                       </div>
                              @endforeach
                               </div>
                           </div>
                        @endforeach
                       
                    </div>
                </div>
                <div>
                
                </div>
               
              
            </div>
            
        </div>
    </div>

@endsection
