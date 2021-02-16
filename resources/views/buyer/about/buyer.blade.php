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
                                          <h3>   {{$company->buyer_company_name}} </h3> <span>{{__('Joined Seifex : ')}} </span>
                                          <span> {{$company->created_at->diffForHumans() }}</span>
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
                                          <h3>  ... </h3>
                                      </div>
                                      <div class="card-body">
                                          {{--@foreach($sales as $dept  =>  $order_value)--}}
                                              {{--{{str_replace('_',' ',$dept)}} {{' : '}} {{$order_value}}--}}
                                              {{--@if(!$loop->last)--}}
                                                  {{--<hr>--}}
                                              {{--@endif--}}
                                          {{--@endforeach--}}
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
                       </div>
                        </div>
                    </div>
               
            </div>
        </div>
    </div>
@endsection
