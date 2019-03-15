<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function () {
    Route::post('play', "MatchController@play")->name('play.all');
    Route::post('play/{week}', "MatchController@playWeekly")->name('play.week');
    Route::get('fixture', "MatchController@index")->name('fixture.list');
    Route::get('league', "LeagueController@index")->name('league.list');
    Route::post('reset', "MatchController@resetFixture")->name('fixture.reset');
    Route::get('matches/{week}', "MatchController@show")->name('matches.show');
    Route::get('predictions', "MatchController@getPredictions")->name('predictions.list');
    Route::patch('matches/{id}/{column}/{value}',"MatchController@update")->name('matches.update');
});



