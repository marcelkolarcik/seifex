@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col">
                        <div class="card ">
                            <div class="card-header bg-secondary text-light  mb-2">
                                @include('owner.admins.nav')
                            </div>
                            <div class="card-body">
                                <div class="container">
                                    <div class="row d-flex justify-content-between  ">
                                            <div class="col">
                                                <div class="accordion" id="dutiesForAdminRoles">
                                                    @foreach($admin_duties as $role    =>  $role_duties)
                       
                                                        <div class="card">
                                                            <div class="card-header bg-secondary text-light" id="{{$role}}">
                                                                <button class="btn btn-link  text-light" type="button" data-toggle="collapse" data-target="#collapse{{$role}}" aria-expanded="true" aria-controls="collapse{{$role}}">
                                                                Duties for :  {{$role}}
                                                                </button>
                                                            </div>
                                                            <div id="collapse{{$role}}" class="collapse" aria-labelledby="{{$role}}" data-parent="#dutiesForAdminRoles">
                                                                <div class="card-body">
                                    
                                                                    @foreach($role_duties as $key=>$duty)
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <p class="card-text">Name : {{$duty->duty_name}}</p>
                                                                            <a href="/owner/edit_duty/{{$duty->id}}">Edit</a>
                                                                        </div>
                                                                        <div>
                                                                            <p class="card-text">Description : <br>{{$duty->duty_description}}</p>
                                                                        </div>
                                                                        <hr>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col">
                                            {!! Form::model( $duty = new \App\AdminDuty, ['url' => 'owner/create_admin_duty']) !!}
                                            @csrf
                                            @include('owner.admins.create_admin_duty_form',['submitButtonText' => 'Create Admin Duty'] )
                                            {!! Form::close() !!}
                                            </div>
                                @include('errors.list')
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection




