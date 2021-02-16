
    <div class="list-group list-group-horizontal-sm  mb-1" >
        <a class="list-group-item  pt-1 pb-1 bg-secondary text-light"  role="tab">
            @if($employee['role']       === 'buyer_buyer')
                {{__('Buyers')}}
            @elseif($employee['role']   === 'buyer_accountant' ||  $employee['role']   === 'seller_accountant')
                {{__('Accounts')}}
            @elseif($employee['role']   === 'seller_seller')
                {{__('Sales')}}
            @elseif($employee['role']   === 'seller_delivery')
                {{__('Delivery')}}
            @endif
        </a>
        <a class="list-group-item list-group-item-action pt-1 pb-1 bg-grey-500 text-light"  role="tab">{{__($employee['staff_position'])}}</a>
        <a class="list-group-item list-group-item-action pt-1 pb-1"  role="tab">{{__($employee['staff_name'])}}</a>
        <a class="list-group-item  pt-1 pb-1"  role="tab">  {{$employee['email']}}</a>
        <a class="list-group-item list-group-item-action pt-1 pb-1"  role="tab">{{__('Out : ')}}  {{$employee['undelegated_at']}}</a>
    </div>

