{{--DELIVERY DAYS--}}

    <div class="list-group list-group-horizontal-sm mt-2 mb-1 col-md-12 text-grey-800 " >
        <a class="list-group-item list-group-item-action pt-1 pb-1  bg-warning disabled" >
            {{__('No sellers available for your preferred currency and language. Please select alternative.')}}</a>
    </div>
    <div class=" mb-1 mt-1 ">
      
        @foreach($alternatives as $currency    =>  $languages)
            @foreach($languages as $language)
                <div  class="btn btn-sm btn-outline-success text-dark  alternative"
            
                      title                    =    "{{$currency.' - '.$language}}"
                      data-currency            =    "{{$currency}}"
                      data-language             =    "{{$language}}"
                      data-wrong               =   "{{__('Something went wrong !')}}"
                      data-later               =   "{{__('Please try again later.')}}"
                >
                    {{ $currency.' - '.$language }}
                </div >
            @endforeach
        @endforeach
    
    </div>


