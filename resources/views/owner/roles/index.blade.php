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
                  
                  @include('owner.roles.nav')
                  
                </div>
                
                <div class="card-body">
                    <h5 class="card-title">Roles created so far...</h5>
                    
                    <table class="table table-sm table-bordered ">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Role for</th>
                            <th scope="col">Name</th>
                            <th scope="col">Created at</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td><small class="text-secondary">{{__('#')}}</small>{{$loop->iteration}}</td>
                                <td>{{$role->guard}}</td>
                                <td>{{$role->guard.'_'.$role->name}}</td>
                                <td>{{$role->updated_at}}</td>
                                <td>
                                   {!!  Form::open(['method' => 'DELETE','action' => [ 'Owner\RoleController@destroy', $role->id]])!!}
                                        @csrf
                                        <input class="btn btn-danger btn-sm" type="submit" value="Delete" >
                                    {!! Form::close() !!}
                                    
                                   
                                </td>
                                
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>

@endsection
