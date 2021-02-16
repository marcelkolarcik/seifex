<a class="stats list-group-item list-group-item-action pt-1 pb-1 bg-secondary text-light top_products
    "
   href=""
   title="{{__(ucwords(str_replace('_',' ',$by)))}}"
   data-year = "{{date('Y')}}"
   @if(isset($period))
       data-period = {{ $period }}
   @endif
   
   @if(substr($by,0,7) == 'by_prod'))
       data-product = {{'product'}}
    @endif
  
   data-by="{{$by}}">
    
    {{__(ucwords(explode('_',$by)[1]))}}
</a>
