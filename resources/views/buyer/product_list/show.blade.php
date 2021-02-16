@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            
            <div class="col-md-12 printable" id="printable">
                <div class="card bg-transparent" >
                    
                        @if(isset($department_deleted))
                            @component('components.main_header_red')
                                {{__('You have no products left. Your department has been deleted !',[__('department') => __(str_replace('_',' ',$department))])}}
                           
                            @endcomponent
                        @else
                            @component('components.label_header_with_button_green')
                            {{ __(str_replace('_',' ',$department)) }}  {{__('products')}}
                                @slot('button')
                                <button class="print d-print-none form-control btn btn-light_green btn-sm float-right"> {{__('Print product list')}}</button>
                                @endslot
                            @endcomponent
                        @endif
                    <div class="d-print-none">
                    
                    </div>
                        @include('buyer.product_list.table')
                        
                        @include('includes.uploadFeedback')
                    
                   
                </div>
            
            
            
            </div>
        </div>
    </div>

@endsection
