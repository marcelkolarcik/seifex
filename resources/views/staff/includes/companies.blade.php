@component('components.label_header_primary')
    {{__('Choose companies new member will be working with')}}
@endcomponent
@if($details['companies'] != null)
@foreach( $details['companies'] as $id  =>  $company)
    <span   class=" list-group-item  pt-1 pb-0 mb-1 ">
        
        @if(isset($scope) && $scope['companies'] != null && in_array($id,$scope['companies']))
            {!! Form::checkbox('companies[]',$id,true ) !!}
        @else
            {!! Form::checkbox('companies[]',$id ) !!}
        @endif
        
        {!! Form::label('companies', $company['company_name'], ['class' => 'text-primary']) !!}
     </span>
@endforeach
@else
    {{__('You have no companies yet.')}}
@endif
