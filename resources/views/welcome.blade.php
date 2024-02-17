<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'ultimatePOS') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.css?v='.$asset_v) }}">
        <link rel="stylesheet" href="{{ asset('css/full.css?v='.$asset_v) }}">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #fff;
                font-family: 'Raleway', sans-serif;
                height: 100vh;
                margin: 0;
                background-color: #ffffff;
                background-image: url("./../public/images/wall3.jpg");
                
                background-repeat: no-repeat;
            }
/*
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
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .tagline{
                font-size:25px;
                font-weight: 300;
            }*/
        </style>
    </head>
    <body>
            <nav class="navbar navbar-fixed-top navbar-inverse" style="background: #3c8dbc;border: 1px solid #3c8dbc; color: #fff;" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">{{ config('app.name', 'ultimatePOS') }}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    @if (Route::has('login'))
                        @if (Auth::check())
                            <li><a href="{{ action('HomeController@index') }}">@lang('home.home')</a></li>
                        @else
                           <li> <a href="{{ action('Auth\LoginController@login') }}">@lang('lang_v1.login')</a></li>
                            @if(env('ALLOW_REGISTRATION', true))
                                <li><a href="{{ route('business.getRegister') }}">@lang('lang_v1.register')</a></li>
                            @endif
                        @endif
                    @endif
                    @if(Route::has('pricing') && config('app.env') != 'demo')
                       <li> <a href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a></li>
                    @endif
                </ul>
                

            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
      <!--   <div class="flex-center position-ref full-height">
            <div class="top-right links">

                @if (Route::has('login'))
                    @if (Auth::check())
                        <a href="{{ action('HomeController@index') }}">@lang('home.home')</a>
                    @else
                        <a href="{{ action('Auth\LoginController@login') }}">@lang('lang_v1.login')</a>
                        @if(env('ALLOW_REGISTRATION', true))
                            <a href="{{ route('business.getRegister') }}">@lang('lang_v1.register')</a>
                        @endif
                    @endif
                @endif

                @if(Route::has('pricing') && config('app.env') != 'demo')
                    <a href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
                @endif
            </div>

            <div class="content">
                <div class="title m-b-md" style="font-weight: 600 !important">
                    {{ config('app.name', 'ultimatePOS') }}
                </div>
                <p class="tagline">
                    {{ env('APP_TITLE', '') }}
                </p>
            </div> -->
        </div>
    </body>
</html>
