<div class="list-group">

    <a class="list-group-item list-group-item-action active mb-2" href="{{ url('/buyer') }}">{{--{{ ucwords(explode('_',Auth::guard('buyer')->user()->role)[1])  }}--}} {{__('Dashboard')}}</a>
  
    @if(isset($staff_companies) && sizeof($staff_companies) == 1)
         @foreach($staff_companies as $company_id   =>  $company)
                <div class="list-group-item">
                    <a class="" href="{{ url('/buyer/company',$company_id) }}">{{$company->buyer_company_name}} </a><br>
                </div>
         @endforeach
        @if(isset($departments))
            @foreach($departments as $key => $department)
                 @if($department != '')
                     <a class="list-group-item  d-flex justify-content-between align-items-center"
                        href={{ url('/department/'.str_replace(' ','_',$department)) }} >
                         {{str_replace('_',' ',$department)}}
                      </a>
                 @endif
            @endforeach
            @if (Auth::guard('buyer')->user()->can('manage_products', App\ProductList::class))
                   @if(explode('/',$_SERVER['REQUEST_URI'])[1]  != 'product_list' )
                          <a class="list-group-item bg-secondary text-light"  href={{ url('/product_list/'.$company_id) }} >{{__('Add or Edit products')}}</a>
                   @endif
             @endif
        @endif
    @else
        @foreach($staff_companies as $company_id   =>  $company)
            <div class="list-group-item">
                <a class="" href="{{ url('/buyer/company',$company_id) }}">{{$company->buyer_company_name}} </a>
            </div>
        @endforeach
    @endif
</div>
