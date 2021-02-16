@extends($guard.'.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
            @include($guard.'.includes.left_side')
            </div>
            <div class="col-md-9 ">
                <div class="card mb-2">
                    @component('components.main_header')
                        {{__('Default Departments')}}
                    @endcomponent
                    <div class="card-body">
                        <div class="list-group">
                       @foreach($default_departments as $default_department)
                                <div class="list-group-item d-flex justify-content-between align-items-center ">
                           {{$default_department->department}}
                            <a href="../edit_department/{{$default_department->id}}">{{__('Edit')}}</a>
                                
                                </div>
                        @endforeach
                        </div>
                    </div>
                </div>
                @include('default_departments.create')
            </div>
            
        </div>
    </div>

@endsection
