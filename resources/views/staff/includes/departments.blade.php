@component('components.label_header_primary')
    {{__('Departments')}}
@endcomponent


    {{--IF ACCOUNTANT THERE WILL BE NULL DEPARTMENT--}}
    @foreach($details['departments'] as $key =>$department)
        @if(isset($scope) && $scope['departments'] && in_array($department,$scope['departments']))
            {!! Form::checkbox('departments[]', $department,true) !!}
        
        @else
            {!! Form::checkbox('departments[]', $department,false) !!}
        @endif
        
        {!! Form::label('departments',\App\Services\StrReplace::currency_underscore(__($department)) , ['class' => 'text-primary']) !!}
        <br>
    @endforeach

