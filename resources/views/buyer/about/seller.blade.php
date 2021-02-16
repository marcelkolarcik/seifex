@extends('buyer.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
               @include('includes.buyer.left_side')
            </div>
            <div class="col-md-9">
                
                @include('buyer.about.nav')
               
                    <div class="card">
                        <div class="card-body">
                        <div class="row">
                              <div class="col-md-6 mb-2">
                                  <div class="card">
                                      <div class="card-header bg-primary text-light">
                                          <h5>   {{$seller_company->seller_company_name}} </h5>
                                          <span>{{__('Joined Seifex : ')}} </span>
                                          <span> {{$seller_company->created_at->diffForHumans() }}</span>
                                      </div>
                                     <div class="card-body">
                                          @foreach(json_decode($seller_company->address,true) as $line)
                                               {{$line}}
                                              @if(!$loop->last)
                                                  {{' | '}}
                                              @endif
                                          @endforeach
                                              <hr>
                                         @foreach($location as $loc)
                                                  {{str_replace('-',' ',$loc)}}
                                                  @if(!$loop->last)
                                                      {{' | '}}
                                                  @endif
                                         @endforeach
                                     </div>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="card">
                                      <div class="card-header bg-primary text-light">
                                          <h5>  {{__('Sales')}} </h5>
                                      </div>
                                      <div class="card-body">
                                          @foreach($sales as $dept  =>  $order_value)
                                              {{__(str_replace('_',' ',$dept))}} {{' : '}} {{$order_value}}
                                            
                                              @if(!$loop->last)
                                                  <hr>
                                              @endif
                                          @endforeach
                                      </div>
                                  </div>
                              </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary text-light">
                                          {{__('Delivery Days')}}
                                        <hr>
                                        @if($delivery_days !== [])
                                            @foreach($delivery_days as $department=>$days)
                                               {{str_replace('_',' ',$department)}}
                                                <hr>
                                                @foreach($days as $day)
                                                @if ($loop->last)  {{  __($day)  }}
                                                @else
                                                    {{__($day) .','}}
                                                @endif
                                                @endforeach
                                            @endforeach
                                        
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary text-light">
                                          {{__('Invoice frequency')}}
                                        <hr>
                                     
                                        @foreach($frequencies as $department    =>  $frequency)
                                            {{str_replace('_',' ',$department)}}  {{ __($frequency) }}
                                            <hr>
                                        @endforeach
                                       
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                       <div class="row">
                           <div class="col-md-4">
                               <div class="card">
                                   <div class="card-header">
                                       {{__('Owner')}}
                                   </div>
                                   <ul class="list-group list-group-flush">
                                       <li class="list-group-item">{{$seller_company->seller_owner_name}}</li>
                                       <li class="list-group-item">{{$seller_company->seller_owner_email}}</li>
                                       <li class="list-group-item">{{$seller_company->seller_owner_phone_number}}</li>
                                   </ul>
                               </div>
                           </div>
                           <div class="col-md-4">
                               <div class="card">
                                   <div class="card-header">
                                       {{__('Seller')}}
                                   </div>
                                   <ul class="list-group list-group-flush">
                                       <li class="list-group-item">{{$seller_company->seller_name}}</li>
                                       <li class="list-group-item">{{$seller_company->seller_email}}</li>
                                       <li class="list-group-item">{{$seller_company->seller_phone_number}}</li>
                                   </ul>
                               </div>
                           </div>
                           <div class="col-md-4 mb-2">
                               <div class="card">
                                   <div class="card-header">
                                       {{__('Acountant')}}
                                   </div>
                                   <ul class="list-group list-group-flush">
                                       <li class="list-group-item">{{$seller_company->seller_accountant_name}}</li>
                                       <li class="list-group-item">{{$seller_company->seller_accountant_email}}</li>
                                       <li class="list-group-item">{{$seller_company->seller_accountant_phone_number}}</li>
                                   </ul>
                               </div>
                           </div>
                           <div class="col-md-12 ">
                               <div class="card">
                                   <div class="card-header bg-secondary text-info">
                                       {{__('Their Buyers : ')}}
                                   </div>
                                  
                                   <ul class="list-group list-group-flush">
                                       @foreach($their_buyers as $buyer_company_id  =>  $buyer_company_name)
                                       <li class="list-group-item">
                                           <a href="/buyer/about/{{$buyer_company_id}}/buyer">{{$buyer_company_name}}</a>
                                       </li>
                                      
                                       @endforeach
                                   </ul>
                               </div>
                           </div>
                       </div>
                        </div>
                    </div>
               
            </div>
        </div>
    </div>
@endsection
