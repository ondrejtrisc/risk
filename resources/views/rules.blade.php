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
                <div class="card-header">Rules</div>

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
                              Here will be rules.
                            </div>
                        </div>
                    {{-- @endauth --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
