@if(session('buyer_accountant_not_checked'))
    <div class="alert alert-danger">
        <small> {{session('buyer_accountant_not_checked')}}</small>
    </div>
@endif
