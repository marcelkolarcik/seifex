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
                    <h5 class="card-title">Admins created so far...</h5>
                    <h6 class="card-subtitle mb-2 text-muted">You can de-activate any of them...</h6>
                    
                    <table class="table table-sm table-bordered ">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Email</th>
                            <th scope="col">Since</th>
                            <th scope="col">Active</th>
                            <th scope="col">Duties</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{--'name','role','email','created_at'--}}
                        @foreach($admins as $admin)
                           
                                    @if($admin->suspended   ==  null)
                                        <tr >
                                            <td >
                                        <form  action="{{ URL::to('/owner/admin/deactivate/'.$admin->id) }}"  method="post" enctype="multipart/form-data">
                                            @csrf
            
                                            {!! Form::submit('de-activate', ['class' => 'btn btn-sm btn-danger btn-sm ']) !!}
        
                                        </form>
                                            </td>
                                    @else
                                        <tr  class="bg-warning">
                                            <td>
                                        <form  action="{{ URL::to('/owner/admin/activate/'.$admin->id) }}"  method="post" enctype="multipart/form-data">
                                            @csrf
            
                                            {!! Form::submit('activate', ['class' => 'btn btn-sm btn-success btn-sm ']) !!}
        
                                        </form>
                                            </td>
                                    @endif
                                <td>{{$admin->name}}</td>
                                <td>{{$admin->role}}</td>
                                <td>{{$admin->email}}</td>
                                <td>{{$admin->created_at}}</td>
                                @if($admin->suspended == null)
                                <td >Yes</td>
                                @else
                                    <td class="bg-danger text-light">No</td>
                                @endif
                                <td><a href="/owner/admin/assign/{{$admin->id}}">duties</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if($delegated_admins)
                    <hr>
                    <h5 class="card-title">Admins delegated so far...</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Did not accepted job yet...</h6>
    
                    <table class="table table-sm table-bordered bg-warning">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Email</th>
                            <th scope="col">Delegated</th>
                            <th scope="col">Active</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{--'name','role','email','created_at'--}}
                        @foreach($delegated_admins as $admin)
                            <tr >
                                <td>{{$admin->delegated_name}}</td>
                                <td>{{$admin->delegated_role}}</td>
                                <td>{{$admin->delegated_email}}</td>
                                <td>{{$admin->delegated_at}}</td>
                                <td>No</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
