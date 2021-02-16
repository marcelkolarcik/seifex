@if(session()->has('department_created'))
    @component('components.main_header_green')
        {{__(session('department_created'))}}  {{__('department was created.')}}
    @endcomponent
@elseif(session()->has('department_deleted'))
    @component('components.main_header_red')
        {{__(session('department_deleted'))}}  {{__('department was deleted.')}}
    @endcomponent

@elseif(session()->has('changed_products'))
    @component('components.label_header_with_2button_green')
        {{__('Your recent changes')}}
        @slot('button')
        {{session()->get('changed_department')}}
        @endslot
        @slot('button2')
            @if(sizeof($languages['all']) > 1)
            {{ session()->has('changed_products') ? __('Do you need to check your translations ?'): ''}}
            @endif
        @endslot
    @endcomponent
    <table class="table table-bordered table-sm table-responsive-sm">
        <thead>
        <tr>
          
            <th scope="col">{{__('old product')}}</th>
            <th scope="col">{{__('change')}}</th>
            <th scope="col">{{__('new product')}}</th>
        </tr>
        </thead>
        
        @foreach(session()->get('changed_products') as $change => $products)
          
            @foreach($products as $old_product => $new_product)
               
                @if($change == 'added')
                    <tr>
                        <td> </td>
                        <td> {{ $change}}</td>
                        <td> {{$new_product}}</td>
                    </tr>
                @elseif($change == 'edited')
                    <tr>
                       
                        <td> {{$old_product}}</td>
                        <td> {{ $change.' to'}}</td>
                        <td> {{$new_product}}</td>
                    </tr>
                @elseif($change == 'deleted')
                    <tr>
                      
                        <td> {{$old_product}}</td>
                        <td> {{ $change}}</td>
                        <td> </td>
                    </tr>
                @elseif($change == 'translated')
                    <tr>
                        <td> {{$old_product}}</td>
                        <td> {{__($change.' to')}} {{session()->get('changed_language')}}</td>
                        <td> {{$new_product}}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
    </table>
@endif
