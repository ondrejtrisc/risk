@extends('layouts.app')

@section('content')

<style>
    body{
        height: 100vh;
        background-color: #fff;
        background-image: url("/images/background.jpg");
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-size: cover;
    }

    form{
        padding: 0.2em;
    }

    button{
    min-width: 150px;
    }

</style>   

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="background-color: rgb(248,224,176, .9)">
                <div class="card-header" style="background-color: rgb(40,16,16, 1)">
                    <h3 class="text-center" 
                    style=
                    "font-family: 'Bree Serif', serif; 
                    font-weight: 200; 
                    font-weight: 600; 
                    color:#F8E0B0; 
                    text-transform: uppercase;
                    letter-spacing: .1rem;

                    ">Lobby of games</h3>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @auth
                        <div class="col">
                            {{-- <form action="" method="get">
                                <button type="submit">User settings</button>
                            </form>   --}}
                            {{-- <form action="{{ action('GameController@create') }}" method="get" class="row justify-content-center">
                                <button type="submit" class="btn btn-secondary btn-sm">Create a new game</button>
                            </form>   --}}
                            {{-- <form action="{{ action('GameController@index') }}" method="get">
                                <button type="submit">Go to the game list</button>
                            </form>   --}}
                            <div id="lobby" class="row justify-content-center" style="background-color: rgb(248,224,176, 0)"></div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
