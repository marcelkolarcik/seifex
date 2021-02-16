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
    
                        <ul class="nav nav-tabs ">
                            <li class="nav-item">
                            BACKUP {{date('Y-m-d D')}}
                            </li>
                        </ul>
                    
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title">Create back up.</h5>
                        <h6 class="card-subtitle mb-2 text-muted">You can name it...</h6>
                        <hr>
    
                        <form  action="{{ URL::to('/owner/backup/') }}"  method="post" enctype="multipart/form-data">
                            @csrf
                            {!! Form::checkbox('full_backup', null) !!}
                            {!! Form::label('full_backup', 'Full Backup', ['class' => '']) !!}
                            <hr>
                            {!! Form::label('backup_name', 'Backup name', ['class' => '']) !!}
                            {!! Form::text('backup_name', null,['class' => 'form-control form-control-sm','placeholder'=>'optional']) !!}
                            <hr>
                            {!! Form::submit('BACKUP', ['class' => 'btn btn-sm btn-danger btn-sm ']) !!}
    
                        </form>
                        <hr>
                        
                        @if(session()->has('href'))
                            <h6 class="card-subtitle mb-2 text-muted">Link to backup...</h6>
                            <a href="{{session()->get('href')}}"  >{{session()->get('href')}}</a>
                            <hr>
                            <h6 class="card-subtitle mb-2 text-muted">Folders backed up...</h6>
                           @foreach(session()->get('files') as $file)
                               {{$file}}
                               @if(!$loop->last)
                                   |
                               @endif
                           @endforeach
                        @endif
                    </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
