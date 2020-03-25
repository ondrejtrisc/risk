@extends('layouts.app')

@section('content')


<div class="container">
  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-header">Details</div>
                @auth
                <div class="card-body">
                  <h3>Game #{{$game->id}}</h3>
                  <p><strong>Founder: </strong>{{$usernames_arr[$game->founder_user_id]}}</p>
                  <p><strong>Players: </strong><br> 
                    @foreach($game_users as $u)
                      <p>
                        {{$u->name}}
                      </p>
                    @endforeach
                  </p>
                  <p><strong>Maximum number of players: </strong>{{$game->max_players}}</p>
                  <p><strong>Initial troops deployment: </strong>{{$game->init_deployment}}</p>

                  {{-- <p><strong>Status: </strong>
                    @if(array_search(\Auth::user(), $game_users))
                      {{'you joined this game'}}
                    @else
                      {{$game->status}}
                    @endif --}}
                  {{-- </p> --}}

                  @if($game->status != 'launched')

                    @if(\Auth::user()->id == $game->founder_user_id)  
                      @if($num_users == $game->max_players)
                        <form action="{{ action('GameController@launch', [$game->id]) }}" method="get">
                          <button type="submit">Launch the game</button>
                        </form>
                      @endif
                    @endif

                    @if($u == \Auth::user() && \Auth::user()->id != $game->founder_user_id)
                      <form action="{{ action('GameController@leave', [$game->id]) }}" method="post">
                        @csrf
                        <button type="submit">Leave this game</button>
                      </form>
                    @endif

                    @if(\Auth::user()->id != $game->founder_user_id && $num_users < $game->max_players
                    && array_search(\Auth::user(), $game_users) == false)
                      <form action="{{ action('GameController@update', [$game->id]) }}" method="post">
                        @csrf
                        <button type="submit">Join</button>
                      </form>
                    @endif

                    @if(\Auth::user()->id == $game->founder_user_id)  
                      <form action="{{ action('GameController@delete', [$game->id]) }}" method="post">
                        @method("delete")
                        @csrf
                        <button type="submit">Delete</button>
                      </form>
                    @endif

                  @else
                    @if(array_search(Auth::user(), $game_users)  > -1)
                      <form action="{{ action('GameController@play', [$game->id]) }}" method="get">
                        <button type="submit">Play</button>
                      </form>
                    @endif  

                  @endif

                  <form action="{{ action('GameController@index') }}" method="get">
                    @csrf
                    <button type="submit">Back to list of games</button>
                  </form>
                </div>
              @endauth

              @guest
                <div class="card-body">
                  <p>Please <a href="{{ route('login') }}">log in</a></p>
                </div>
              @endguest

          </div>
        </div>
    </div>
</div>
  
@endsection