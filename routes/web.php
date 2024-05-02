<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// 追加
Route::get('/index', 'App\Http\Controllers\OAuthController@index');
Route::get('/auth', 'App\Http\Controllers\OAuthController@auth');