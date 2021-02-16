@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                
                <div class="card">
                    <div class="card-header bg-secondary text-light  mb-2">
                        @include('owner.admins.nav')
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2 ">
                        <h5 class="card-title">{{$admin->name}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{$admin->email}}</h6>
                            
                            @if($admin->suspended   ==  null)
                            <form  action="{{ URL::to('/owner/admin/deactivate/'.$admin->id) }}"  method="post" enctype="multipart/form-data">
                                @csrf
        
                                {!! Form::submit('de-activate', ['class' => 'btn btn-danger btn-sm form-control']) !!}
    
                            </form>
                            @else
                                <form  action="{{ URL::to('/owner/admin/activate/'.$admin->id) }}"  method="post" enctype="multipart/form-data">
                                    @csrf
            
                                    {!! Form::submit('activate', ['class' => 'btn btn-success btn-sm form-control']) !!}
        
                                </form>
                            @endif
                        </div>
                        @include('includes.feedback')
                        <form  action="{{ URL::to('/owner/admin/assign/'.$admin->id) }}"  method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="container">
                                <ul >
        
                                    @foreach($default_admin_duties as $duty_name =>  $duty)
                                     
                                        @if($admin->duties != null)
                                          
                                            @if(in_array($duty_name,json_decode($admin->duties,true)))
                    
                                                <li class="list-group-item bg-secondary text-light">
                                                    <input name="duties[]" class="form-check-input" type="checkbox"  value="{{$duty[0]->duty_name}}" checked>
                                                    <label class="form-check-label text-capitalize" >{{$duty[0]->duty_description}}</label>
                                                </li>
                                            @else
                                                <li class="list-group-item">
                                                    <input name="duties[]" class="form-check-input" type="checkbox"  value="{{$duty[0]->duty_name}} ">
                                                    <label class="form-check-label text-capitalize" >{{$duty[0]->duty_description}}</label>
                                                </li>
                                            @endif
                                        @else
                                            <li class="list-group-item">
                                                <input name="duties[]" class="form-check-input" type="checkbox"  value="{{$duty[0]->duty_name}} ">
                                                <label class="form-check-label text-capitalize" >{{$duty[0]->duty_description}}</label>
                                            </li>
                                        @endif
                                    @endforeach
        
        
                                    <hr>
                                   
        
                                    <div class="form-group submit_button">
                                        {!! Form::submit('Save changes', ['class' => 'btn btn-success form-control']) !!}
                                    </div>
                                </ul>


                            </div>
                        </form>
                    </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
