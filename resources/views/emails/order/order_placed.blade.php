<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <style type="text/css" rel="stylesheet" media="all">
        /* Media Queries */
        @media  only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; -moz-hyphens: auto; -ms-word-break: break-all; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word; margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;">
<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
    <tr>
        <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;" align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                <!-- Logo -->
                <tr>
                    <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                        <a style="box-sizing: border-box; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;" href="http://127.0.0.1:8000" target="_blank">
                            Seifex.com
                        </a>
                    </td>
                </tr>
                
                <!-- Email Body -->
                <tr>
                    <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;" width="100%">
                        <table style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; width: auto; max-width: 570px; margin: 0 auto; padding: 0;" align="center" width="570" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="box-sizing: border-box; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; padding: 35px;">
                                    @if($buyer == null)
                                        {{-- email to seller --}}
                                    <!-- Greeting -->
                                    <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;">
                                        {{__('Hello')}} {{$details['seller_company_name']}}! <br>
                                    
                                    </h1>
                                   
                                    <!-- Intro -->
                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; text-align: left; margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;">
                                        {{$details['subject_seller']}}
                                            <hr>
                                       
                                    </p>
                                    @else
                                        {{-- email to buyer --}}
                                        <!-- Greeting -->
                                            <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;">
                                                {{__('Hello')}} {{$details['buyer_company_name']}}! <br>
        
                                            </h1>
        
                                            <!-- Intro -->
                                            <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; text-align: left; margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;">
                                                {{$details['subject_buyer']}}
                                                <br>
                                                {{__('You will receive confirmation email, once the order is dispatched.')}} <br>
                                            </p>
                                            <hr>
                                            
                                    @endif
                                    
                                    <table class="table table-sm table-striped ">
                                        <tr style="border-bottom: groove; border-top: groove">
                                            <td>{{__('Qty')}}</td>
                                            <td>{{__('Unit')}}</td>
                                            <td>{{__('Product')}}</td>
                                            <td>{{__('Code')}}</td>
                                        </tr>
                                        @foreach($details['order'] as $product=>$data)
                                            <tr>
                                                @if(isset($data['box_size']))
                                                    <td>{{$data['amount']}}</td>
                                                    <td>box <small>{{$data['box_size']}} kg</small></td>
                                                    <td >{{$product}}<br><small class="text-primary"  >{{$data['type_brand']}}</small></td>
                                                    <td>{{$data['product_code']}}</td>
                                                @else
                                                    <td>{{$data['amount']}}</td>
                                                    <td>kg</td>
                                                    <td>{{$product}}<br><small class="text-primary"  >{{$data['type_brand']}}</small></td>
                                                    <td>{{$data['product_code']}}</td>
                                                @endif
                                            </tr>
                                    @endforeach
                                    </table>
    
                                    <hr>
                                    <small>
                                      {{__('For full details about the orders, please visit your Seifex account.')}}
                                    </small>
                                    <br> <br> <br>
                                    <!-- Action Button -->
                                    
                                    
                                    <!-- Outro -->
                                    
                                    
                                    <!-- Salutation -->
                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; text-align: left; margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;">
                                        {{__('Regards,')}}<br>Seifex
                                    </p>
                                    
                                    <!-- Sub Copy -->
                                
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <!-- Footer -->
                <tr>
                    <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;" align="center" width="570" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="box-sizing: border-box; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #AEAEAE; padding: 35px; text-align: center;">
                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; text-align: left; margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                        © {{date('Y')}}
                                        <a style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #3869D4;" href="http://127.0.0.1:8000" target="_blank">Seifex.com</a>.
                                        {{__('All rights reserved.')}}
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
