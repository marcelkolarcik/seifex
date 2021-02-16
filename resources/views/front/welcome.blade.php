<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
        <link href="/css/app.css" rel="stylesheet">
    
        
    </head>
    <body>
        <div id="app" class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    <div class="py-5 text-center">
                        <img class="d-block mx-auto mb-4" src={{ asset('/logos/Seifex-logo-hat-300-full.png') }}  >
                    </div>
                </div>
               
                    <div class="links">
                        @if (Route::has('login'))
            
                            @auth
                                {{-- <a href="{{ url('/home') }}">Home</a>--}}
                            @else
                                <a href="{{ url('buyer/login') }}">{{__('Buyer')}} {{__('Login')}}</a>
                                <a href="{{ url('seller/login') }}">{{__('Seller')}} {{__('Login')}}</a>
                             
                                <a href="{{ url('admin/login') }}">{{__('Admin')}} {{__('Login')}}</a>
                                <a href="{{ url('owner/login') }}">{{__('Owner')}} {{__('Login')}}</a>
                                <br> <br>
                                {{-- @if (Route::has('register'))
									 <a href="{{ route('register') }}">Register</a>
								  @endif--}}
                                <hr>
                                <a href="{{ url('buyer/register') }}">{{__('Buyer')}} {{__('Register')}}</a>
                                <a href="{{ url('seller/register') }}">{{__('Seller')}} {{__('Register')}}</a>
                               {{-- <a href="{{ url('admin/register') }}">Admin Register</a>
                                <a href="{{ url('owner/register') }}">Owner Register</a>--}}
                
                                {{--  <a href="{{ url('register_admin_admin') }}">Register Admin</a>--}}
            
                            @endauth
        
                        @endif
                            <hr>
                            <div class="container">
                                <div class="row">
                                    <div class="card col-md-8 offset-md-2 align-self-center ">
                                        <div class="card-header">
                                            {{__('Seifex.com active in')}} {{$number_of_countries}} {{__('countries')}}.
                                        </div>
                                        <div class="card-body">
                                            @include('front.current_countries')
                                        </div>
                                        <div class="card-footer">
                                            <a href="/new_country"> {{__('Request your country')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
               
            </div>
            
           
        </div>
       
    </body>
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <script src="/js/app.js"></script>
</html>
