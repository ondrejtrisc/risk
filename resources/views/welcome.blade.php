<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Risk: the board game</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                background-image: url("/images/background.jpg");
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
                color: #636b6f;
                /* font-family: 'Nunito', sans-serif; */
                font-family: 'Bree Serif', serif;
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

            a:link {
                color: #281010;
            }

            .content {
                text-align: center;
            }

            .title {
                color: #281010;
                font-size: 200px;
            }
            .subtitle {
                color: #281010;
                font-size: 50px;
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
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    <a href="{{ url('/description') }}" style="color: #F8E0B0;">Description</a>
                    <a href="{{ url('/rules') }}" style="color: #F8E0B0;">Rules</a>
                    @auth
                    <a href="{{ url('/home') }}" style="color: #F8E0B0;">Home</a>
                    @else
                        <a href="{{ route('login') }}" style="color: #F8E0B0;">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" style="color: #F8E0B0;">Register</a>
                            @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    RISK
                </div>

                <div class="subtitle m-b-md">
                    THE BOARD GAME
                </div>


                <div class="links">
                    {{-- <a href="./description" style="color: #281010; font-size: 16px">Description and rules</a> --}}
                    {{-- <a href="https://laracasts.com" style="color: #281010;">Laracasts</a>
                    <a href="https://laravel-news.com" style="color: #281010;">News</a>
                    <a href="https://blog.laravel.com" style="color: #281010;">Blog</a>
                    <a href="https://nova.laravel.com" style="color: #281010;">Nova</a>
                    <a href="https://forge.laravel.com" style="color: #281010;">Forge</a>
                    <a href="https://vapor.laravel.com" style="color: #281010;">Vapor</a>
                    <a href="https://github.com/laravel/laravel" style="color: #281010;">GitHub</a> --}}
                </div>
            </div>
        </div>
    </body>
</html>
