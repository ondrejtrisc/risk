@extends('layouts.app')

@section('content')

<style>
    form{
        padding: 0.2em;
      }
    
      button{
        min-width: 150px;
      }

</style>   

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Welcome to Risk</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @auth
                        <div class="col">
                            <h2 class="row justify-content-center">Hi {{ Auth::user()->name }}, let's play!)</h2>
                            {{-- <form action="" method="get">
                                <button type="submit">User settings</button>
                            </form>   --}}
                            {{-- <form action="{{ action('GameController@create') }}" method="get" class="row justify-content-center">
                                <button type="submit" class="btn btn-secondary btn-sm">Create a new game</button>
                            </form>   --}}
                            {{-- <form action="{{ action('GameController@index') }}" method="get">
                                <button type="submit">Go to the game list</button>
                            </form>   --}}
                            <div id="lobby" class="row justify-content-center"></div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
