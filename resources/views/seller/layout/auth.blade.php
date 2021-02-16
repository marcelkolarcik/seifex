<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Multi Auth Guard') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" />
   
    <!-- Scripts -->
    {{--<script src="https://kit.fontawesome.com/78adefaacc.js"></script>--}}
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
<div id="app" class="container">
    <nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light">
    
        <a class="navbar-brand d-flex justify-content-between align-items-center" href="{{ url('/seller') }}">
            <img class="d-block mx-auto" src={{ asset('/logos/Seifex-logo-hat-150-full.png') }}  > &nbsp;{{-- &nbsp; Seller--}}
        <h3>{{session()->get('company_id')}}</h3> &nbsp;
            &nbsp;
            <a href="/seller/company/{{session()->get('company_id')}}">
                <small>{{session()->get('seller_company_name')}}</small>
            </a>
            {{--<small>@if(\Auth::guard('seller')->check()) {{\Auth::guard('seller')->user()->role}}</small>  @endif--}}
        </a>
    
       
        
        @if (Auth::guard('seller')->guest())
            <a href="{{ url('/seller/login') }}">{{__('Login')}}</a> |
            <a href="{{ url('/seller/register') }}">{{__('Register')}}</a>
        @else
         @include('includes.notification_bell')
        
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                @if(session()->has('company_id'))
                <ul class="navbar-nav mr-auto">
                    
                    {{--<li class="nav-item active">--}}
                        {{--<a title="Info about your company" class="nav-link" href="/seller/about">About <span class="sr-only">(current)</span></a>--}}
                    {{--</li>--}}
                 
    
    
                    @if (Auth::guard('seller')->user()->can('seller_see_orders', App\Order::class))
                    <li class="nav-item">
                        <a class=" nav-link   align-items-center  {{ isset($orders_active)     ? $orders_active     : ''  }}"
                           href="/orders">
                            {{__('Orders')}}
                        </a>
                    </li>
                    @endif
                   {{-- <li class="nav-item dropdown">--}}
                        {{--<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                            {{--Dropdown--}}
                        {{--</a>--}}
                        {{--<div class="dropdown-menu" aria-labelledby="navbarDropdown">--}}
                            {{--<a class="dropdown-item" href="#">Action</a>--}}
                            {{--<a class="dropdown-item" href="#">Another action</a>--}}
                            {{--<div class="dropdown-divider"></div>--}}
                            {{--<a class="dropdown-item" href="#">Something else here</a>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                   
                    @if (Auth::guard('seller')->user()->can('seller_see_invoices', App\Invoice::class))
                    <li class="nav-item">
                        <a class=" nav-link d-flex justify-content-between align-items-center "
                           href="/invoices">
                            {{__('Invoices')}}
                        </a>
                    </li>
                    @endif
                   
                    
                    @if (Auth::guard('seller')->user()->can('seller_coordinate_requests', App\ProductList::class))
                    <li class="nav-item">
                        <a class=" nav-link d-flex justify-content-between align-items-center  {{ isset($product_lists_active) ? $product_lists_active : ''  }}"
                           href="/requests">
                            {{__('Cooperation requests')}}
        
                        </a>
                    </li>
                    @endif
    
                    @if (Auth::guard('seller')->user()->can('seller_see_invoices', App\Invoice::class))
                        <li class="nav-item">
                            <a class=" nav-link d-flex justify-content-between align-items-center "
                               href="/statistics">
                                {{__('Statistics')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class=" nav-link d-flex justify-content-between align-items-center "
                               href="/charts">
                                {{__('Chart')}}
                            </a>
                        </li>
                    @endif
                </ul>
                @endif
                
                <ul  class="navbar-nav mr-right ">
                    <li class="nav-item dropdown " >
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::guard('seller')->user()->name }} <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ url('/buyer/logout') }}"
                               onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                                {{__('Logout')}}
                                <form id="logout-form" action="{{ url('/seller/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </a>
                            @if(\Auth::guard('seller')->user()->role === 'seller_owner')
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item"  href="{{ url('/create_seller_company') }}">{{__('Create Company')}}</a>
                            @endif
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
    </nav>
    @include('includes.notifications')
</div>

    @yield('content')

    <!-- Scripts -->
    <script src="/js/app.js"></script>

</body>
</html>
