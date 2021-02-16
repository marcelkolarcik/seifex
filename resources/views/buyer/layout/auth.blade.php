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
    <nav class="navbar navbar-expand-lg navbar-light " >
        
        <a class="navbar-brand d-flex justify-content-between align-items-center" href="{{ url('/buyer') }}">
            <img class="d-block mx-auto "  src={{ asset('/logos/Seifex-logo-hat-150-full.png') }}  > &nbsp; &nbsp;
        </a>
       
        {{--<h3>{{session()->get('company_id') }} &nbsp;  </h3>--}}
        <a href="/buyer/company/{{session()->get('company_id')}}">
            <small>  {{session()->get('buyer_company_name')}}</small></a>
      
        @if (Auth::guard('buyer')->guest())
            <a href="{{ url('/buyer/login') }}">{{__('Login')}}</a> |
            <a href="{{ url('/buyer/register') }}">{{__('Register')}}</a>
        @else
            @include('includes.notification_bell')
        
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            @if(session()->has('company_id'))
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto {{--nav  nav-pills--}}">
                    @if (Auth::guard('buyer')->user()->can('buyer_see_orders', App\Order::class) && Auth::guard('buyer')->user()->role !== 'buyer_accountant')
                    <li class="nav-item">
                        <a class="nav-link" href="/ordering">{{__('Ordering')}} </a>
                    </li>
                    @endif
                        @if (Auth::guard('buyer')->user()->can('buyer_see_orders', App\Order::class))
                    <li class="nav-item">
                        <a class="nav-item nav-link   align-items-center {{ isset($orders_active)     ? $orders_active     : ''  }}"
                           href="/orders">
                            {{__('Orders')}}
                        </a>
                    </li>
                        @endif
                    @if(\Auth::guard('buyer')->user()->role !== 'buyer_accountant')
                            @if (Auth::guard('buyer')->user()->can('buyer_coordinate_requests', App\ProductList::class))
                            <li class="nav-item">
                                <a class="nav-item nav-link d-flex justify-content-between align-items-center {{ isset($product_lists_active) ? $product_lists_active : ''  }}"
                                   href="/requests">
                                    {{__('Cooperation requests')}} &nbsp;
                                </a>
                            </li>
                            @endif
                    @endif
                    @if (Auth::guard('buyer')->user()->can('buyer_see_invoices', App\Invoice::class))
                    <li class="nav-item">
                        <a class="nav-item nav-link d-flex justify-content-between align-items-center  {{ isset($invoices_active) ? $invoices_active : ''  }}"
                           href="/invoices">
                            {{__('Invoices')}}
                        </a>
                    </li>
                            <li class="nav-item">
                                <a class=" nav-link d-flex justify-content-between align-items-center "
                                   href="/charts">
                                    {{__('Chart')}}
                                </a>
                            </li>
                    @endif
                        @if(App\BuyerCompany::where('buyer_id',Auth::guard('buyer')->user()->id)->first())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{__('About')}}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="/buyer/about">{{__('About')}}</a>
                            <a class="dropdown-item" href="/buyer/about/product_lists">{{__('Our Products')}}</a>
                            <a class="dropdown-item" href="/buyer/about/our_sellers">{{__('Our Sellers')}}</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/statistics">{{__('Statistics')}}</a>
                        </div>
                    </li>
                            @endif
                   
                </ul>
                @endif
                <ul  class="navbar-nav mr-right">
    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::guard('buyer')->user()->name }} <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ url('/buyer/logout') }}"
                               onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                                {{__('Logout')}}
                                <form id="logout-form" action="{{ url('/buyer/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </a>
                            @if(\Auth::guard('buyer')->user()->role === 'buyer_owner')
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item"  href="{{ url('/create_buyer_company') }}">{{__('Create Company')}}</a>
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
