@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-secondary text-light">
                       @include('owner.statistics.nav')
                    </div>
                    <div class="card-body">
                        @if(isset($companies))
                            <ul class="list-group">
                                @foreach($companies as $company_id => $sum)
                                    <li class="list-group-item">
                                        <a href="/owner/statistics/{{$type}}/{{$country_id}}/{{$company_id}}"> {{$company_names[$company_id]}} => {{$sum}} EUR</a>
                                       
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                     
                    </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection
