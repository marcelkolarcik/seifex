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
                
                
                <form  action="{{ URL::to('/owner/create_admin_types') }}"  method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="container">
                        <div class="row d-flex justify-content-between  mb-2">
                            <div class="col">
                                <ul>
                                <li class="list-group-item list-group-item-dark text-light" >Available Types : </li><br>
                               
                               @foreach($admin_types as $type)
                                 <li class="list-group-item ">{{$type->admin_type}}
                                     <a href="/owner/delete_admin_type/{{$type->id}}" title="delete {{$type->admin_type}}">
                                         <small class="float-right text-danger">delete</small>
                                     </a>
                                 </li>
                               @endforeach
                                </ul>
                            </div>
                            <hr>
                            <div class="col">
                                <label for="admin_type">Create New Admin Type</label>
                            {{ Form::text('admin_type', null ,['class' => 'form-control form-control-sm','placeholder'=>'New admin type','required']) }}
                           
                                <label for="name"> &nbsp;</label>
                                <button id="submit" class="btn btn-sm btn-primary  form-control  form-control-sm">Create New Admin Type</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

@endsection
