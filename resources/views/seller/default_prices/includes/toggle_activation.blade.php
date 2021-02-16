
@if(isset($info->s_company->price_lists[$info->department][$info->b_company['id']]))
    
    @if($info->action ==  'disabled')
    
        <div class="list-group-item list-group-item-action  pt-1 pb-1 mr-1 list-group-item-warning"  role="tab" >
           {{'not activated by buyer'}}
        </div>
    @elseif($info->action ==  'de_activate')
        
        
        <a title="{{__('Deactivate')}} {{__('buyer')}} ?" type="button"
           href=""
           seller_company_id="{{ $info->s_company->id }}"
           buyer_company_id="{{ $info->b_company['id'] }}"
           department = "{{$info->department}}"
           url = "deactivate_buyer"
           text="{{__('By clicking Yes!, you agree to Terms and Conditions of Seifex.com !')}}"
           wrong       ="{{__('Something went wrong.')}}"
           later        ="{{__('Please try again later.')}}"
           class="toggle_seller list-group-item   list-group-item-danger pt-1 pb-1 btn  mr-1" >
            {{__('Deactivate')}}
        </a>
    
    
    @elseif($info->action ==  'activate')
        
        <a  title="{{__('Activate')}} {{__('buyer')}} ?" type="button"
            href=""
            seller_company_id="{{ $info->s_company->id }}"
            buyer_company_id="{{ $info->b_company['id'] }}"
            department = "{{$info->department}}"
            url = "activate_buyer"
            text="{{__('By clicking Yes!, you agree to Terms and Conditions of Seifex.com !')}}"
            wrong       ="{{__('Something went wrong.')}}"
            later        ="{{__('Please try again later.')}}"
            class="toggle_seller list-group-item  list-group-item-light_green pt-1 pb-1 btn  mr-1" >
            {{__('Activate')}}
        </a>
    
    @endif

@else
    <span title=" {{'create'}}"
    
       class="list-group-item list-group-item-light_green list-group-item-action pt-1 pb-1  mr-1" >
        {{'create'}}
    </span>
   
@endif
