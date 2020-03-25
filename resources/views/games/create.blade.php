@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-header">Create a new game</div>

              <div class="card-body">
                <h3>Create a new game</h3>
                <form action="{{ action('GameController@store')}}" method="post"> 
                  @csrf
                  <label>Number of players:</label>
                  <select name="max_players"><br>
                      <option value="2">2</option>    
                      <option value="3">3</option>    
                      <option value="4">4</option>    
                      <option value="5">5</option>    
                      <option value="6">6</option>    
                  </select><br>
                  <label>Initial troops deployment:</label>
                  <select name="init_deployment"><br>
                      <option value="random">random</option>    
                      <option value="manual">manual</option>    
                  </select><br>
                  <button type="submit">Create game</button>
                </form>
                <form action="{{ action('GameController@index') }}" method="get">
                  @csrf
                  <button type="submit">Back to list of games</button>
                </form>
              </div>
          </div>
      </div>
  </div>
</div>
  
@endsection
