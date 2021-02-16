

@extends($guard.'.layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            
            <div class="col-md-12">
               
                <div class="card">
                    <div class="card-header d-print-none">
                        <!-- List group -->
                        <div class="list-group list-group-horizontal-sm" id="myList" role="tablist">
                            <a  id="yearly_p" class="periods list-group-item list-group-item-action active pt-1 pb-1" data-toggle="list" href="#yearly" role="tab">
                                <span  class="text-danger" style="font-size: 150%">&#x233E; </span> &nbsp; <span > {{__('YEARLY')}}</span>
            
                            </a>
                            <a class="periods list-group-item list-group-item-action pt-1 pb-1 " data-toggle="list" href="#monthly" role="tab">
                                <span  > &#x1F4C8; </span> &nbsp; <span > {{__('MONTHLY')}}</span>
                            </a>
                            <a class=" periods list-group-item list-group-item-action pt-1 pb-1 " data-toggle="list" href="#current" role="tab">
                                <span  > &#x1F4C8; </span> &nbsp; <span >{{date('F')}} &nbsp; {{date('Y')}}</span>
                            </a>
                            <a class="list-group-item list-group-item-action pt-1 pb-1"  role="tab">
                
                                {!! Form::select('year', $years,null  ,[
									   'wrong' =>__('Something went wrong.'),
									   'later' =>__('Please try again later.'),
									   'id' => 'year',
									   'class' => 'form-control form-control-sm bg-light text-secondary',
									   'title'=> __('Pick the year to calculate')
									
									]) !!}
                            </a>
                            <a    class="list-group-item list-group-item-action pt-1 pb-1 products"   role="tab">
                                {!! Form::select('top_products', $top_products,null  ,[
									 'wrong' =>__('Something went wrong.'),
									 'later' =>__('Please try again later.'),
									 'id' => 'top_products',
									 
									 'placeholder' =>  __('# products'),
									 'class' => ' form-control form-control-sm   text-secondary ',
								  
								  ]) !!}
                            </a>
        
                        </div>
        
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="yearly" role="tabpanel">
                                <div class="d-print-none list-group list-group-horizontal-sm mt-2 " >
                                    @component('components.graph.by',['by' => 'by_departments'])
                                    @endcomponent
                                    @if($guard == 'seller')
                                        @component('components.graph.by',['by' => 'by_locations'])
                                        @endcomponent
                                        @component('components.graph.by',['by' => 'by_sellers'])
                                        @endcomponent
                                    @endif
                                    @component('components.graph.by',['by' => 'by_companies'])
                                    @endcomponent
                                    @component('components.graph.by',['by' => 'by_products'])
                                    @endcomponent
                                </div>
                            </div>
                            <div class="tab-pane" id="monthly" role="tabpanel">
                                <div class="d-print-none list-group list-group-horizontal-sm mt-2" >
                    
                                    @component('components.graph.by',['by' => 'by_department_periods'])
                                    @endcomponent
                                    @if($guard == 'seller')
                                        @component('components.graph.by',['by' => 'by_location_periods'])
                                        @endcomponent
                                        @component('components.graph.by',['by' => 'by_seller_periods'])
                                        @endcomponent
                                    @endif
                                    @component('components.graph.by',['by' => 'by_company_periods'])
                                    @endcomponent
                                    @component('components.graph.by',['by' => 'by_product_periods'])
                                    @endcomponent
                                </div>
                            </div>
                            <div class="tab-pane" id="current" role="tabpanel">
                                <div class="d-print-none list-group list-group-horizontal-sm mt-2" >
                    
                    
                                    @component('components.graph.by',['by' => 'by_department_periods','period'    =>  'd'])
                                    @endcomponent
                                    @if($guard == 'seller')
                                        @component('components.graph.by',['by' => 'by_location_periods','period'    =>  'd'])
                                        @endcomponent
                                        @component('components.graph.by',['by' => 'by_seller_periods','period'    =>  'd'])
                                        @endcomponent
                                    @endif
                                    @component('components.graph.by',['by' => 'by_company_periods','period'    =>  'd'])
                                    @endcomponent
                                    @component('components.graph.by',['by' => 'by_product_periods','period'    =>  'd'])
                                    @endcomponent
                                </div>
                            </div>
                        </div>
    
    
                    </div>
                    <div class="card-body mt-0 pt-0 pb-0 " >
                       
                       <div class="row " id="graph_holder" >
                        <div class="col bg-light graph_holder  border border-grey-300" >
                        
                           
                            <div id="stats" >
                                <a class=" list-group-item list-group-item-action pt-1 pb-1 border-0 d-none d-print-block"
                                > {{date('M d Y : D')}} </a>
                               
                                <div class="d-print-none list-group list-group-horizontal-sm mt-2 " >
                                    <a class="d-print-none list-group-item list-group-item-light_green
                            pt-1 pb-1 border-0 btn btn-sm text-secondary"
                                       id="printer"
                                       title="{{__('Save as PDF')}}">
                                        {{__('Save as PDF')}}
                                    </a>
                                </div>
                                @include('statistics.graph')
                            </div>
                        </div>
                        <div id="percentage" class="col-md-4 border border-grey-300 bg-light graph_holder ">
                          
                           
                            <a class="figures list-group-item list-group-item-action  pt-1 pb-1 bg-light_green text-secondary d-print-none mt-1"
                               data-by="by_departments_figures"
                               href=""
                               data-year = "{{date('Y')}}"
                               title="  {{__('By departments')}}">
                                {{__('FULL FIGURES')}}
                            </a>
                           
                           {{-- <button class="d-print-none btn btn-sm btn-light_green text-secondary float-md-left" id="printer"> {{__('Print')}}</button>--}}
                        <ul id="perc" class="list-group pt-3 " >
                          
                      
                            @foreach($details['percentage'] as $name => $sum)
                            <li class="list-group-item p-1  border-top-0 border-left-0 border-right-0 mb-1 d-flex justify-content-between align-items-center">
                                <span style="background-color: {{$details['percentage_colors'][$name]}}">&nbsp;&nbsp;
                                </span><span>{{$name}}</span><span class="bg-secondary text-light pl-2 pr-2">{{$sum}}</span> </li>
                            @endforeach
                     
                        </ul>
                        
                        </div>
                       </div>
                       
                    </div>
                    
                    <div class="card-body">
                        <div  class="d-print-none d-none list-group list-group-horizontal-sm mt-2 figure_print" >
                            <a class=" list-group-item list-group-item-light_green
                            pt-1 pb-1 border-0 btn btn-sm text-secondary"
                               id="printer"
                               title="{{__('Save as PDF')}}">
                                {{__('Save as PDF')}}
                            </a>
                        </div>
                        <div id="figures">
                        
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    
@endsection
