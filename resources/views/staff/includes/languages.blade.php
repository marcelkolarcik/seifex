@component('components.label_header_primary')
    {{__('Languages')}}
@endcomponent
<div class="col-md-12">
    @foreach($details['languages'] as $short => $long)
        
        
        @if(isset($scope) && $scope['languages'] != null && in_array($short,$scope['languages']))
            {!! Form::checkbox('staff_languages[]',$short,true ) !!}
        @else
            {!! Form::checkbox('staff_languages[]',$short ) !!}
        @endif
        
        {!! Form::label('languages', $long, ['class' => 'text-dark']) !!}
        <br>
    @endforeach
</div>
