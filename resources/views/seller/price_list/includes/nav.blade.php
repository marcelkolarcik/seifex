

<!-- List group -->
<div class="list-group list-group-horizontal-sm col-md-12 mb-1" id="nav" role="tablist">
    <a class="list-group-item list-group-item-action bg-secondary text-light  pt-1 pb-1  "
       
       href="/pricing/{{$info->b_company['id']}}/{{$info->department}}/{{session()->get('company_id')}}" >
        {{$info->b_company['company_name']}}
    </a>
    <a class="list-group-item list-group-item-action active pt-1 pb-1  " data-toggle="list" href="#price_list" role="tab"> {{__('Prices')}}</a>
    <a class="list-group-item list-group-item-action  pt-1 pb-1 " data-toggle="list" href="#info" role="tab"> {{__('Info')}}</a>
    <a class="list-group-item list-group-item-action disabled  bg-secondary text-light pt-1 pb-1
            "
       data-toggle="list" href="#department" role="tab">
        {{!isset($info->department) ?__('Department'): __(str_replace('_',' ',$info->department))   }}
    </a>
   
</div>
