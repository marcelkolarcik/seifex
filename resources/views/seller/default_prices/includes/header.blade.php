<div class="row">
    

        <div   class="col" > {{__('Department')}}
            @include('seller.default_prices.includes.select_department')
        </div>
   
        <div   class="col"  >
            {{__('Currency')}} {!! Form::select('currency',$currencies, isset($currency)  ? $currency : $c_preferred_currency,[
                                        'class'=>'form-control form-control-sm extended_price_list',
                                        'id'    =>  'currency',
                                        'name'    =>  'currency',
                                       'placeholder'=>__('Please select currency'),
                                       isset($first_time) && $first_time == true ?'disabled' :'',
                                       
                                        ]) !!}
        </div>
    
    
        <div    class="col" >
            {{__('Languages')}} {!! Form::select('language',$available_languages, isset($language) ? $language : null,[
                                        'class'=>'form-control form-control-sm extended_price_list',
                                        'id'    =>  'language',
                                        'name'    =>  'language',
                                         'placeholder'=>__('Please select language'),
                                          isset($first_time) && $first_time == true ?'disabled' :'',
                                       
                                        ]) !!}
        </div>
   
    @if( ( isset($first_time) && $first_time == true ) )
        {{--BECAUSE DISABLED SELECT IS NOT IN THE FORM REQUEST--}}
        {{  Form::text('currency', isset($preferred_currency)  ? $preferred_currency : $currency,['class'=>'d-none'])}}
        {{  Form::text('language',$language,['class'=>'d-none'])}}
        
        <div class="col-md-12">
            @component('components.main_header_green')
        {{__('This is your first price list, so create it in your preferred language and currency')}}
            @endcomponent
        </div>
    @endif
</div>
