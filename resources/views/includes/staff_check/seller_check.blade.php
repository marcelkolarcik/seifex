@if(session('seller_not_checked'))
    <div class="alert alert-danger">
        <small> {{session('seller_not_checked')}}</small>
    </div>
@endif
