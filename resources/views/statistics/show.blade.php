@extends($guard.'.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            
            <div class="col-md-12">
               
                <div class="card">
                    <div class="card-header bg-secondary text-light">
                        {{__('STATISTICS')}}
                      
                    </div>
                    
                    <div class="card-body">
                       @include('includes.company_statistics')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
