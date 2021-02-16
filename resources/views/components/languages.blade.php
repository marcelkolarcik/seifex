<div class="card-header mb-2">
 
    <label for="languages">{{__('Languages')}} : </label>
    <br>
   
    @foreach($languages as $language)
       
            {{$language}}
            @if(!$loop->last)
                ,
            @endif
       
    @endforeach
</div>

