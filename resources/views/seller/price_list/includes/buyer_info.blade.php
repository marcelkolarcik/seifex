{{--BUYER INFO--}}

<div class="card ">
    <div class="card-body">
        <div class="row mb-1 {{$info->action == 'activate' ? 'border-danger': ''}} ">
            <div class="bg-light text-dark img-thumbnail col-md-3">
                <label for="location">{{__('Company')}}</label>
                
                <span class="text-black-50 ">
                                <h5>{{$info->b_company['company_name']}} </h5>
                                <label>{{str_replace('_',' ',session('department'))}} </label> <br>
                                <label for="currency">{{__('Currency')}} : </label><br>
                                <label for="turnover">{{__('Turnover')}} : </label>
                                 </span>
            </div>
            <div class="bg-light text-dark img-thumbnail col-md-3">
                <label for="location">{{__('Contact details')}}</label>
                <br>
                <small class="text-black-50 ">
                    
                    <label>{{str_replace('_',' ',session('department'))}} </label> <br>
                    <label for="currency">{{__('Currency')}} : </label><br>
                    <label for="turnover">{{__('Turnover')}} : 12 234 </label><br>
                    <label>{{str_replace('_',' ',session('department'))}} </label> <br>
                    <label for="currency">{{__('Currency')}} : </label><br>
                    <label for="turnover">{{__('Turnover')}} : 12 234 </label>
                </small>
            </div>
            <div class="bg-light text-dark img-thumbnail col-md-3">
                <label for="location">{{__('Location')}}</label>
                <br>
                <span class="text-black-50 ">
                               
                               @foreach(explode(' : ',$info->full_path) as $part)
                        {{$part}}
                        @if(!$loop->last)
                            <br>
                        @endif
                    @endforeach
                                
                                </span>
            </div>
            <div class="bg-light text-dark img-thumbnail col-md-3">
                <label for="location">{{__('Address')}}</label>
                <br>
                <span class="text-black-50">
                                {{$info->b_company['address']['city']}}<br>
                    {{$info->b_company['address']['line_1']}}<br>
                    {{$info->b_company['address']['line_2']}}<br>
                    {{$info->b_company['address']['line_3']}}<br>
                    {{$info->b_company['address']['postal_code']}}
                                </span>
            </div>
            
            @if($info->action == 'activate')
                <div class="bg-light_green text-danger col-md-12 text-center mt-1">
                    <span> {{__('Activate')}}  {{$info->b_company['company_name']}}.</span>
                </div>
            @endif
        </div>
        
        <div class="row mt-1 ">
            <div class="col-md-4 img-thumbnail  ">
                <ul>
                    <li>
                        {{__('Fill in the prices and sizes where applicable.')}}
                    </li>
                    <li>
                        {{__('Save it for buyer to use your prices !')}}
                    </li>
                    <li>
                        {{__('If you don\'t have certain product, write Zero')}} <code>0</code>{{__(', in')}} <b>{{__('Price per kg')}}</b> {{__('Column !')}}
                    </li>
                </ul>
                <hr/>
                {{__('We will add the prices,that you are applying here, to your default price list for future use.')}}
            </div>
            <div class="col-md-4 img-thumbnail ">
                
                <label for="delivery_days">
                    @component('components.label_header_with_button_green')
                        {{__('Delivery days - type',[__('type')=>__($info->delivery_days_type)])}}
                        @slot('button')
                            @if (Auth::guard('seller')->user()->can('edit_delivery_days', App\DeliveryLocation::class))
                                <button
                                    class="btn btn-sm btn-outline-success update_buyer_delivery_days"
                                    department            =   "{{session('department')}}"
                                    buyer_company_id      =   "{{session('buyer_company_id')}}"
                                    buyer_name            =   "{{$info->b_company['company_name']}}"
                                    seller_company_id     =   "{{session('seller_company_id')}}"
                                    title                 =   "{{__('Updating delivery days for :')}}"
                                    wrong                 =     "{{__('Something went wrong.')}}"
                                    later                 =     "{{__('Please try again later.')}}"
                                
                                >{{__('Update')}}
                                </button>
                            @endif
                        @endslot
                    @endcomponent
                </label>
                
                
                
                <hr>
                @include('includes.forms.delivery_days')
            </div>
            <div class="col-md-4 img-thumbnail ">
                
                <div class="d-flex justify-content-between align-items-center">
                    <label for="payment_frequency">{{__('Payment frequency')}}</label>
                    @if (Auth::guard('seller')->user()->can('edit_payment_frequency', App\Invoice::class))
                        <button id="payment_frequency"
                                class="btn btn-sm btn-outline-success"
                                department            =   "{{session('department')}}"
                                buyer_company_id      =   "{{session('buyer_company_id')}}"
                                seller_company_id     =   "{{session('seller_company_id')}}"
                                title                 =     "{{__('Change payment frequency ?')}}"
                                text                  =     "{{__('Did you inform your buyer ?')}}"
                                wrong              ="{{__('Something went wrong.')}}"
                                later              ="{{__('Please try again later.')}}"
                        >
                            {{__('Update')}}
                        </button>
                    @endif
                </div>
                <hr>
                <div class="form-check payment_frequency bg-light text-dark img-thumbnail">
               
                    @foreach($info->payment_frequency as $frequency)
                        
                        @if($info->buyer_payment_frequency == $frequency)
                          
                            <input name="payment_frequency" class="form-check-input  payment_frequency" type="radio"
                                   value="{{array_flip($info->payment_frequency)[$info->buyer_payment_frequency]}}" checked>
                            <label class="form-check-label text-success" >{{__($info->buyer_payment_frequency)}}</label>
                        
                        @else
                            <input name="payment_frequency" class="form-check-input payment_frequency" type="radio"
                                   value="{{array_flip($info->payment_frequency)[$frequency]}}">
                            <label class="form-check-label " >{{__($frequency)}}</label>
                        
                        @endif
                        <br>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row mt-1 ">
            <div class="{{$info->matches['currency']  == '' ? 'text-danger' : 'bg-transparent text-dark'}} img-thumbnail col-md-6">
                @component('components.currencies',['currencies'=>$info->b_company['all_currencies']])
                @endcomponent
            </div>
            <div class="{{ $info->matches['language']  == '' ? 'text-danger' : 'bg-transparent text-dark'}}  img-thumbnail col-md-6">
                @component('components.languages',['languages'=>$info->b_company['all_languages']])
                @endcomponent
            </div>
        </div>
        <div class="row mt-1 ">
            <div class="{{ $info->matches['currency']  == '' ? 'text-danger' : 'bg-transparent text-dark'}} img-thumbnail col-md-6">
                <label for="currencies">OUR</label>
                @component('components.currencies',['currencies'=>$info->s_company->all_currencies])
                @endcomponent
            </div>
            <div class="{{ $info->matches['language']  == '' ? 'text-danger' : 'bg-transparent text-dark'}}  img-thumbnail col-md-6">
                <label for="languages">OUR</label>
                @component('components.languages',['languages'=>$info->s_company->all_languages])
                @endcomponent
        
            </div>
        </div>
    </div>
</div>
