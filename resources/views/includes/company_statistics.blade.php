<div class="accordion" id="countries">
    
    @foreach($statistics as $department=>$list)
        
        <button class="btn btn-link " type="button" data-toggle="collapse" data-target="#collapse{{$department}}" aria-expanded="true" aria-controls="collapse{{$department}}">
            {{str_replace('_',' ',$department)}} {{'( '.sizeof($list).' products )'}}
        </button>
        
        <div id="collapse{{$department}}" class="collapse" aria-labelledby="{{$department}}" data-parent="#countries">
            <ul class="list-group">
                
                
                @foreach($list as $product=>$product_data)
                    
                    <li class="list-group-item">
                        {{$product}} | {{$product_data['amount']}} kg &nbsp;|&nbsp;  {{$product_data['order_value']}}&nbsp; EUR
                    </li>
                
                
                
                
                
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
