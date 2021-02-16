{{--DELIVERY DAYS--}}

    <div class="list-group list-group-horizontal-sm mt-2 mb-1 col-md-12" >
        <a class="list-group-item list-group-item-action pt-1 pb-1 bg-grey-500 text-light disabled " >
            {{__('Select day of your delivery!')}}
        </a>
    </div>
    <div class="list-group list-group-horizontal-sm mt-2 mb-1 col-md-12" >
        @foreach($next_days as $day    =>  $dates)
            <button  class="btn btn-sm  btn-outline-primary   list-group-item-action pt-1 pb-1 delivery_date "
                  
                  title                    =    "{{$day}}"
                  data-delivery_date       =   "{{ $dates['en_timestamp']  }}"
                  data-day_num             =   "{{ $dates['day_num']  }}"
                  data-buyer_company_id    =   "{{sizeof($companies) === 1 ? array_key_first($companies) : '' }}"
                  data-department           =   "{{ sizeof($companies[   array_key_first($companies) ]->product_lists) === 1
                  ? array_key_first($companies[   array_key_first($companies) ]->product_lists) : '' }}"
                  data-wrong               =   "{{__('Something went wrong !')}}"
                  data-later               =   "{{__('Please try again later.')}}"
            >
                {{ $dates['display_date'] }}
            </button >
        @endforeach
    </div>
   

