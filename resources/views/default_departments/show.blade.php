@extends($guard.'.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('includes.goods')
            </div>
            <div class="col-md-6 ">
                <div class="card card-default">
                    <div class="card-heading">
                        {{$default_department->department}}
                    </div>{{--{{dd($company)}}--}}

                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
