
    <div class="accordion" id="countries">
       
        @foreach($countries as $continent=>$countriesArray)
            
            <button class="btn btn-link " type="button" data-toggle="collapse" data-target="#collapse{{$continent}}" aria-expanded="true" aria-controls="collapse{{$continent}}">
                {{str_replace('-',' ',$continent)}} {{'( '.sizeof($countriesArray).' )'}}
            </button><br>
            
            <div id="collapse{{$continent}}" class="collapse" aria-labelledby="{{$continent}}" data-parent="#countries">
                @foreach($countriesArray as $key=> $country_data)
                    @if(isset($new_country))
                      
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="country"
                                   id="{{$country_data->country_name}}"
                                   value="{{$country_data->seifex_country_id}}{{'@'}}{{$country_data->country_name}}">
                            <label class="form-check-label" for="{{$country_data->country_name}}">
                                {{$country_data->country_name}}
                            </label>
                        </div>
                    @else
                        <label class="form-check-label" >{{$country_data->country_name}}</label>
                        <br>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>

