@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3 ">
                @include('includes.seller.left_side')
            </div>
            <div class="col-md-9 ">
                <div class="card  ">
                    <div class="card-header  bg-secondary text-light">
                        {{__('Invoice frequency')}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                        
                       @foreach($buyers as $bc_id  =>  $data)
                             @foreach($data['price_list'] as $department    =>  $price_list)
                                <form action="">
                           <div class="card bg-transparent border-info text-primary" >
                             <div class="card-header bg-transparent border-info">
                                 
                                 <div class="accordion" id="buyers">
                                     
                                     <button class="btn btn-link" type="button"
                                             data-toggle="collapse" data-target="#buyer_info"
                                             aria-expanded="true" aria-controls="buyer_info">
                                         {{$data['company']['company_name']}} &#11167;&#11165;
                                     </button>
                                     
                                     <div id="buyer_info" class="collapse"  data-parent="#buyers">
                                      {{__('Accountant')}}   <br>
                                         <small> {{$data['company']['buyer_accountant_name']}}</small> <br>
                                         <small> {{$data['company']['buyer_accountant_phone_number']}}</small> <br>
                                         <small> {{$data['company']['buyer_accountant_email']}}</small> <br>
                                     </div>
                                    
                                   </div>
                                 <small>{{__(str_replace('_',' ',$department))}}</small>
                             </div>
                           
                           
                            <div class="card-body ml-2">
                            @foreach($frequencies as $key=>$frequency)
            
                                @if($price_list['payment_frequency'] == $key)
                
                                    <input name="payment_frequency" class="form-check-input  payment_frequency" type="radio"  value="{{$key}}" checked>
                                    <label class="form-check-label text-success" >{{__($frequency)}}</label>
            
                                @else
                                    <input name="payment_frequency" class="form-check-input payment_frequency" type="radio"  value="{{$key}}" >
                                    <label class="form-check-label" >{{__($frequency)}}</label>
            
                                @endif
                                    <br>
                            @endforeach
                            </div>
                               <div class="card-footer bg-transparent border-info">
                                   <button id="payment_frequency"
                                           class="btn btn-sm btn-outline-success"
                                           department            =   "{{$department}}"
                                           buyer_company_id      =   "{{$data['company']['id']}}"
                                           seller_company_id     =   "{{$price_list['seller_company_id']}}"
                                           title                 =     "{{__('Change payment frequency ?')}}"
                                           text                  =     "{{__('Did you inform your buyer ?')}}"
                                           wrong              ="{{__('Something went wrong.')}}"
                                           later              ="{{__('Please try again later.')}}"
                                   >
                                       {{__('Update')}}
                                   </button>
                               </div>
                           </div>
                                </form>
                                @endforeach
                       @endforeach
                        </div>
                    </div>
                
                </div>
            </div>{{--end of div.col-md-9--}}
        </div>{{--end of div.row--}}
    </div> {{--end of div.container--}}
        

@endsection
