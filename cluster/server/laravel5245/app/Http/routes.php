<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=>'demo','namespace'=>'Demo','middleware'=> ['web']],function (){

    Route::get('/mysql','MysqlController@Get');
    Route::get('/memcached','MemcachedController@Get');
    Route::get('/redis','RedisController@Get');
});

