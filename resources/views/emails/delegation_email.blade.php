
{{--DELEGATIONS--}}
@if($token)
  
   @include('emails.'.explode('_',$staff_role)[0].'/'.explode('_',$staff_role)[1].'_delegated'))
   
{{--UN-DELEGATIONS--}}
@else
   
   @include('emails.'.explode('_',$staff_role)[0].'/'.explode('_',$staff_role)[1].'_undelegated'))
@endif



