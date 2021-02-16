@extends('owner.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                @include('owner.includes.left_side')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-primary text-light mb-3">
                        Seifex.com income by country:
                    </div>
               
                <div class="container">
                    <div class="row">
                        <div class="card w-50 bg-secondary border-light text-light mb-3">
                            <div class="card-header border-light">
                            Total Sales
                            </div>
                            <div class="card-body border-light">
                                <h5 class="card-title">10000000 EUR </h5>
                                <p class="card-text">Some quick example text.</p>
                            </div>
                            <div class="card-footer border-light">
                            details
                            </div>
                        </div>
                        <div class="card bg-secondary border-light text-light w-50 mb-3" >
                            <div class="card-header border-light ">Total Income</div>
                            <div class="card-body border-light ">
                                <h5 class="card-title">3500000 EUR</h5>
                                <p class="card-text">Some quick example</p>
                            </div>
                            <div class="card-footer border-light">details</div>
                        </div>
                        
                            
                            <div class="card border-secondary " style="width: 33%" >
                                <div class="card-header bg-transparent border-secondary">Current day</div>
                                <div class="card-body text-success">
                                    <h5 class="card-title">Sales : <small>289334 EUR</small></h5>
                                    <h5 class="card-title">Income : <small>8503 EUR</small></h5>
                                   
                                </div>
                                <div class="card-footer border-secondary">Details</div>
                            </div>
                            <div class="card border-secondary "  style="width: 33%">
                                <div class="card-header bg-transparent border-secondary">Current month</div>
                                <div class="card-body text-success">
                                    <h5 class="card-title">Sales : <small>8680020 EUR</small></h5>
                                    <h5 class="card-title">Income : <small>303800.7 EUR</small></h5>
                                </div>
                                <div class="card-footer bg-transparent border-secondary">Details</div>
                            </div>
                            <div class="card border-secondary"  style="width: 33%">
                                <div class="card-header bg-transparent border-secondary">Current year</div>
                                <div class="card-body text-success">
                                    <h5 class="card-title">Sales : <small>104160240 EUR</small></h5>
                                    <h5 class="card-title">Income : <small>3 645 608 EUR</small></h5>
                                </div>
                                <div class="card-footer bg-transparent border-secondary">Details</div>
                            </div>
                        
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

@endsection
