@extends('front.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 " >
                @include('includes.feedback')
                <form  action="{{ URL::to('/new_country') }}"  method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
    
                        <div class="col-md-4">
                            <label> {{__('Choose your country')}} </label>
                            @include('front.current_countries')
                        </div>
                        
                        <div class="col-md-4">
                            
                            <label for="requester_email">{{__('Your email :')}} </label>
                            {{ Form::email('requester_email', null, ['class' => 'form-control form-control-sm ','required']) }}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                        <label for="submit">{{__('Send request')}}</label>
                        <button id="submit" class="btn btn-sm btn-primary  form-control  form-control-sm">{{__('Send request')}}</button>
                        </div>
                        
                    </div>
                   
                </form>
               
            </div>
        </div>
    </div>
@endsection
