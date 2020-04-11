<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/description', function () {
    return view('description');
});
Route::get('/rules', function () {
    return view('rules');
});


Route::get('/games', 'GameController@index');
Route::get('/games/create', 'GameController@create');
Route::get('/games/{id}', 'GameController@show');
Route::post('/games/store', 'GameController@store');
Route::post('/games/{id}/leave', 'GameController@leave');
Route::post('/games/{id}/launch', 'GameController@launch');
Route::get('/games/{id}/edit', 'GameController@edit');
Route::post('/games/{id}/update', 'GameController@update');
Route::delete('/games/{id}/delete', 'GameController@delete');
Route::post('/occupy/{game_id}', 'GamestateController@occupy');
Route::post('/strengthen/{game_id}', 'GamestateController@strengthen');
Route::post('/playcards/{game_id}', 'GamestateController@play_cards');
Route::post('/deploy/{game_id}', 'GamestateController@deploy');
Route::post('/attack/{game_id}', 'GamestateController@attack');
Route::post('/fortify/{game_id}', 'GamestateController@fortify');
Route::get('/game/{game_id}', 'GameController@play');
Route::get('/{game_id}', 'GamestateController@get_current_state');