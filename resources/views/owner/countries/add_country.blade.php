@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card">
                   
                    <form  action="{{ URL::to('/owner/add_country') }}"  method="post" enctype="multipart/form-data">
                        @csrf
                        @include('owner.includes.current_countries')
                    </form>
                    </div>
                
                </div>
            </div>
        </div>
   

@endsection
