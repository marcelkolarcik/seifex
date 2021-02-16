<!DOCTYPE html>
<html>
<head>
    <title>{{__('Hmm. Something\'s wrong.')}}</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            color: #B0BEC5;
            display: table;
            font-weight: 100;
            font-family: 'Lato', sans-serif;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 72px;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <br><br><br>
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src={{ asset('/logos/Seifex-logo-hat-300-full.png') }}  >
        </div>
    
        <br><br><br>
        <div class="title">{{__('Hmm. Something\'s wrong.')}}</div>
    </div>
</div>
</body>
</html>
