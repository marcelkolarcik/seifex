@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
    
            <!-- List group -->
           @include('seller.price_list.includes.nav')
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="price_list" role="tabpanel"> @include('seller.price_list.includes.price_list_table')</div>
                <div class="tab-pane" id="info" role="tabpanel"> @include('seller.price_list.includes.buyer_info')</div>
            </div>
          
        </div>
        <div class="d-none">
        
            
        </div>
    </div>
@endsection
