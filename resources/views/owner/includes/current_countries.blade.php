<div class="card-header bg-secondary text-light  mb-2">
    
    @include('owner.countries.nav')
    @if(!isset($countries_active))
    <label for="submit"> &nbsp;</label>
    <button id="submit" class="btn btn-sm btn-primary  form-control  form-control-sm">Save changes</button>
    @endif
    @include('includes.feedback')
</div>

<div class="card-body">
    <div class="accordion" id="countries">
        
        @foreach($countries as $continent=>$countriesArray)
           
            <button class="btn btn-link " type="button" data-toggle="collapse" data-target="#collapse{{$continent}}" aria-expanded="true" aria-controls="collapse{{$continent}}">
                {{str_replace('-',' ',$continent)}} {{'( '.sizeof($countriesArray).' )'}}
            </button>
            
            <div id="collapse{{$continent}}" class="collapse" aria-labelledby="{{$continent}}" data-parent="#countries">
                @foreach($countriesArray as $country_id=> $country)
                    @if(!isset($countries_active))
                    <input name="ids[]" class="form-check-input" type="checkbox" value="{{$country_id/*.'---'.$continent*/}}"  >
                    @endif
                    
                    @if(isset($countries_active))
                            <a href="/country/{{$country_id}}">{{$country}}</a>
                    @else
                            <label class="form-check-label" >{{$country}}</label>
                    @endif
                    
                    <br>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
