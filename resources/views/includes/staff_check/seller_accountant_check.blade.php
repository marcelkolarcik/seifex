@if(session('seller_accountant_not_checked'))
    <div class="alert alert-danger">
        <small> {{session('seller_accountant_not_checked')}}</small>
    </div>
@endif
