<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game;
use App\User;
use App\Http\Controllers\GamestateController;

class GameController extends Controller
{
    private function usersIdStrToArrOfUsersIds($game){
        $str_users_ids = $game->users_ids;
        $arr_users_ids = explode(';', $str_users_ids);
        // dd($arr_users_ids);
        return $arr_users_ids;
    }


    private function usersIdStrToArrOfUsers($game){
        $str_users_ids = $game->users_ids;
        $arr_users_ids = explode(';', $str_users_ids);
        foreach($arr_users_ids as $user_id){
            $users_arr[] = User::findOrFail($user_id);
        }
        return $users_arr;
    }

    public function index(){
        // $game_users = [];
        // $num_users = [];
        // $usernames_arr =[];

        $games = Game::all();
        $users = User::all();
        foreach($users as $u){
            $usernames_arr[$u->id] = $u->name;
        }
        foreach($games as $game){
            $game_users[$game->id] = $this->usersIdStrToArrOfUsers($game);
            $num_users[$game->id] = count($this->usersIdStrToArrOfUsers($game));
        }
        // var_dump($game_users);
        return view('games/index', compact('games', 'game_users', 'usernames_arr', 'num_users'));
    }

    public function show($id){
        $game = Game::findOrFail($id);
        $users = User::all();
        foreach($users as $u){
            $usernames_arr[$u->id] = $u->name;
        }
        $game_users = $this->usersIdStrToArrOfUsers($game);
        $num_users = count($this->usersIdStrToArrOfUsers($game));
        return view('games/show', compact('game', 'game_users', 'usernames_arr', 'num_users'));  
    }

    public function create(){
        return view('games/create');
    }

    public function store(Request $request){
        $game = new Game;
        $game->founder_user_id = \Auth::user()->id;
        $game->users_ids = \Auth::user()->id;
        $game->max_players = $request->input('max_players');
        $game->status = 'you can join this game';
        $game->save();
        return redirect('/games/'. $game->id);
    }

    public function leave(Request $request, $id){
        $game = Game::findOrFail($id);
        // $game_users = $this->usersIdStrToArrOfUsers($game);
        $user_id = strval(\Auth::id());
        $users_ids = strval($game->users_ids);
        $strpos = strpos($users_ids, ';'.$user_id);
        $new_users_ids = substr_replace($users_ids, '', $strpos);
        // var_dump($new_users_ids);
        $game->users_ids = $new_users_ids;
        $game->status = 'you can join the game';
        $game->save();

        return redirect('/games');
    }

    // public function edit($id){
    //     $game = Game::findOrFail($id);
    //     $users = User::all();
    //     return view('games/edit', compact('game', 'users'));
    // }

    public function update(Request $request, $id){
        $game = Game::findOrFail($id);
        $game->users_ids = $game->users_ids.';'.\Auth::id();
        // var_dump();
        if ($game->max_users - 1 == count($this->usersIdStrToArrOfUsers($game))) { 
            $game->status = 'ready';
        }
        $game->save();
        return redirect('/games/'. $game->id);
    }

    public function delete($id){
        $game = Game::findOrFail($id);
        $game->delete();
        return redirect('/games');
    }

    public function launch($id){
        $game = Game::findOrFail($id);
        // dd($this->usersIdStrToArrOfUsersIds($game));
        $game->status = 'launched';
        $game->save();
        $colours = ['red', 'blue', 'green', 'yellow', 'brown', 'purple'];
        $user_colours = array_slice($colours, 0, count($this->usersIdStrToArrOfUsers($game)));
        // dd($user_colours);
        $gamestate = new GamestateController;
        $gamestate->create_initial($id, $user_colours);
        $colours = ['red', 'blue', 'green', 'yellow', 'brown', 'purple'];
        $game = Game::findOrFail($id);
        $game_id = $id;
        $index = array_search(\Auth::id(), $this->usersIdStrToArrOfUsersIds($game));
        $colour = $colours[$index];
        $user_name = \Auth::user()->name;
        return view('map/map', compact('game_id', 'colour', 'user_name')); //     'game/'.$id
    }

    public function play($id) {
        $colours = ['red', 'blue', 'green', 'yellow', 'brown', 'purple'];
        $game = Game::findOrFail($id);
        $game_id = $id;
        $index = array_search(\Auth::id(), $this->usersIdStrToArrOfUsersIds($game));
        $colour = $colours[$index];
        $user_name = \Auth::user()->name;
        return view('map/map', compact('game_id', 'colour', 'user_name')); //     'game/'.$id
    }
}
