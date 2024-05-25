<?php

use Illuminate\Support\Facades\Route;

// ホーム
Route::get('/', 'App\Http\Controllers\OAuthController@index');
// 認証後のリダイレクト
Route::get('/auth', 'App\Http\Controllers\OAuthController@auth');