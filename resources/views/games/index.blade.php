@extends('layouts.app')

@section('content')

<style>
  td{
    padding: 0.5em;
    margin: 0.5em;
  }

  form{
    padding: 0;
  }


</style>

<div class="container">
  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-header">List of games</div>

              @auth
                <div class="card-body">
                  <h1>Join one of the games below</h1>
                  <form action="{{ action('GameController@create') }}" method="get">
                    <button type="submit">Or create a new game</button>
                  </form>  
                  <table>
                    <thead>
                      <th>ID</th>
                      <th>Founder</th>
                      <th>Players</th>
                      <th>Maximum players</th>
                      <th>Initial troops deployment</th>
                      <th>Status</th>
                    </thead>
                    <tbody>
                      @foreach ($games as $game)
                        <tr>
                          <td>{{$game->id}}</td>
                          <td>{{$game_users[$game->id][0]->name}}</td>
                          <td>
                            @foreach($game_users[$game->id] as $u)
                              {{$u->name}} <br>
                            @endforeach
                          </td>
                          <td>{{$game->max_players}}</td>
                          <td>{{$game->init_deployment}}</td>
                          <td>
                            {{-- @if(array_search(\Auth::user(), $game_users))
                              {{'you joined this game'}}
                            @else
                              {{$game->status}}
                            @endif --}}
                          </td>
                          <td>
                            <div style="display: flex">
                              @if($game->status != 'launched')
                                <div class="btn-div">
                                  <form action="{{ action('GameController@show', [$game->id]) }}" method="get">
                                    @csrf
                                    <button type="submit">Show</button>
                                  </form>
                                </div>
                                <div class="btn-div">
                                  @if(Auth::user()->id != $game->founder_user_id && $num_users[$game->id]<$game->max_players 
                                  && array_search(\Auth::user(), $game_users[$game->id]) == false)
                                    <form action="{{ action('GameController@update', [$game->id]) }}" method="post">
                                      @csrf
                                      <button type="submit">Join</button>
                                    </form>
                                  @endif
                                </div>
                                <div class="btn-div">
                                  @if(Auth::user()->id == $game->founder_user_id)  
                                    <form action="{{ action('GameController@delete', [$game->id]) }}" method="post">
                                      @method("delete")
                                      @csrf
                                      <button type="submit">Delete</button>
                                    </form>
                                  @endif
                                </div>
                                <div>
                                  @if(Auth::user()->id == $game->founder_user_id)  
                                    @if($num_users[$game->id] == $game->max_players)
                                      <form action="{{ action('GameController@launch', [$game->id]) }}" method="get">  
                                        <button type="submit">Launch the game</button>
                                      </form>
                                    @endif
                                  @endif
                                </div>
                              @else
                                <div class="btn-div">
                                  @if(array_search(\Auth::user(), $game_users[$game->id]) > -1)
                                    <form action="{{ action('GameController@play', [$game->id]) }}" method="get">
                                      <button type="submit">Play</button>
                                    </form>
                                  @endif  
                                </div>
                              @endif
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
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