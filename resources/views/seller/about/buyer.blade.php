@extends('seller.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
           
            <div class="col-md-12">
              
                @include('seller.about.nav')
               
                    <div class="card">
                        <div class="card-body">
                        <div class="row">
                              <div class="col-md-6 mb-2">
                                  <div class="card">
                                      <div class="card-header bg-primary text-light">
                                          <h3>   {{$company->buyer_company_name}} </h3> <span>{{__('Joined Seifex :')}} </span> <span> {{$company->created_at }}</span>
                                      </div>
                                     <div class="card-body">
                                          @foreach(json_decode($company->address,true) as $line)
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
                                          <h3>  {{__('Sales')}} </h3>
                                      </div>
                                      <div class="card-body">
                                          @foreach($sales as $department  =>  $order_value)
                                              
                                              {{__(str_replace('_',' ',$department))}} : {{$order_value}}
                                              @if(!$loop->last)
                                                  <hr>
                                              @endif
                                          @endforeach
                                      </div>
                                  </div>
                              </div>
                        </div>
                            <div class="row">
                                <div class="col-md-6">
                                    @component('components.languages',['languages'=>$languages])
                                    @endcomponent
                                </div>
                                <div class="col-md-6">
                                    @component('components.currencies',['currencies'=>$currencies])
                                    @endcomponent
                                </div>
                            </div>
                       <div class="row">
                           <div class="col-md-4">
                               <div class="card">
                                   <div class="card-header">
                                       {{__('Owner')}}
                                   </div>
                                   <ul class="list-group list-group-flush">
                                       <li class="list-group-item">{{$company->buyer_owner_name}}</li>
                                       <li class="list-group-item">{{$company->buyer_owner_email}}</li>
                                       <li class="list-group-item">{{$company->buyer_owner_phone_number}}</li>
                                   </ul>
                               </div>
                           </div>
                           <div class="col-md-4">
                               <div class="card">
                                   <div class="card-header">
                                       {{__('Buyer')}}
                                   </div>
                                   <ul class="list-group list-group-flush">
                                       <li class="list-group-item">{{$company->buyer_name}}</li>
                                       <li class="list-group-item">{{$company->buyer_email}}</li>
                                       <li class="list-group-item">{{$company->buyer_phone_number}}</li>
                                   </ul>
                               </div>
                           </div>
                           <div class="col-md-4">
                               <div class="card">
                                   <div class="card-header">
                                       {{__('Acountant')}}
                                   </div>
                                   <ul class="list-group list-group-flush">
                                       <li class="list-group-item">{{$company->buyer_accountant_name}}</li>
                                       <li class="list-group-item">{{$company->buyer_accountant_email}}</li>
                                       <li class="list-group-item">{{$company->buyer_accountant_phone_number}}</li>
                                   </ul>
                               </div>
                           </div>
                           <div class="col-md-12 ">
                               <div class="card">
                                   <div class="card-header bg-secondary text-info">
                                       {{__('Their Sellers :')}}
                                   </div>
            
                                   <ul class="list-group list-group-flush">
                                       @foreach($their_sellers as $seller_company_id  =>  $seller_company_name)
                                           <li class="list-group-item">
                                               <a href="/seller/about/{{$seller_company_id}}/seller">{{$seller_company_name}}</a>
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
