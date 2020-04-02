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
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Description</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- @auth --}}
                        <div class="col">
                            {{-- <h2 class="row justify-content-center">Hi {{ Auth::user()->name }}, let's play!)</h2> --}}
                            <div class="row justify-content-center">
                              Risk is a strategy board game of diplomacy, conflict and conquest for two to six players. 
                              The standard version is played on a board depicting a political map of Earth, divided into 
                              forty-two territories, which are grouped into six continents. Turn rotates among players who 
                              control armies of playing pieces with which they attempt to capture territories from other 
                              players, with results determined by dice rolls. Players may form and dissolve alliances during 
                              the course of the game. The goal of the game is to occupy every territory on the board and in 
                              doing so, eliminate the other players. The game can be lengthy, requiring several hours to 
                              multiple days to finish.                            
                            </div>
                        </div>
                    {{-- @endauth --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
