<form  action="{{ URL::to('/companies/transfer') }}"  method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
<div class="row mt-2 ">
   
        <div class="col-md-12">
            @component('components.label_header_primary')
                {{__('Here you can transfer companies between staff, useful when you fire someone, or when some of your staff leave...')}}
            @endcomponent
        </div>
   
        <div class=" col-md-6 col-lg-4">
            @component('components.label_header_secondary_light')
                {{__('Companies from staff')}}
            @endcomponent
                @foreach($sellers_with_companies as $seller_name =>  $companies)
                    <div>
                      <span class="text-primary">
                      {{$seller_name}}
                      </span> <br>
                    @foreach($companies as $bc_id   =>    $company)
                       
                        <span class="ml-5">
                                <input name="price_request_ids[{{$company->seller_id}}]"
                                       class="form-check-input {{$company->seller_id}}"
                                       type="checkbox"
                                       value="{{ $company->price_request_ids }}">
                                <label class="form-check-label " >{{$company->buyer_company_name}}</label>
                                <br>
                        </span>
                  
                    @endforeach
                    </div>
                @endforeach
        </div>
        <div class="col-md-6 col-lg-4">
            @component('components.label_header_secondary_light')
                {{__('To staff')}}
            @endcomponent
                @foreach($to_sellers as $seller_name =>  $seller_id)
                    <div class="ml-4">
                  
                        <input name="to_seller"
                               class="form-check-input to_seller"
                               type="radio"
                               value="{{$seller_id}}">
                        <label class="form-check-label " >  {{$seller_name}}</label>  <br>
                       
                  
                    </div>
                @endforeach
        </div>
    
   
    <div class="col-md-6 col-lg-4">
        @component('components.label_header_with_button_green')
            {{__('New staff will be able to manage transferred companies...')}}
            @slot('button')
                {!! Form::submit(__('make transfer'), ['class' => 'btn btn-sm btn-light_green form-control']) !!}
            @endslot
        @endcomponent
    </div>
   
</div>
    <hr>
</form>
