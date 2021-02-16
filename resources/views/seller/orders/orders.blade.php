@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
           
            <div class="col-md-12">
                <div class="card">
                    <div class="list-group list-group-horizontal-sm" id="myList" role="tablist">
                        <a class="list-group-item list-group-item-action active pt-1 pb-1 "  href="/seller" >
                            {{session()->get('seller_company_name')}}
                        </a>
                        <a class="staff_link list-group-item list-group-item-action disabled pt-1 pb-1" data-toggle="list" href="" role="tab">{{__('Orders')}}</a>
                        {{--<a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#seller_accountant" role="tab">Accounts</a>--}}
                        {{--<a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#seller_delivery" role="tab">Delivery</a>--}}
                        {{--<a class="staff_link list-group-item list-group-item-action pt-1 pb-1" data-toggle="list" href="#previous" role="tab">Previous</a>--}}
                    </div>
                </div>
                @include('includes.orders_list')
            </div>
        </div>
    </div>
@endsection



