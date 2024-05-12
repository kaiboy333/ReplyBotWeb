<?php

use Illuminate\Support\Facades\Route;

// リスト表示
Route::get('/slack.getAllTokens', 'App\Http\Controllers\ApiController@index');
// 追加
Route::get('/slack.addToken', 'App\Http\Controllers\ApiController@store');
// 表示
Route::get('/slack.getToken', 'App\Http\Controllers\ApiController@show')->name('show');
// 更新
Route::get('/slack.updateToken', 'App\Http\Controllers\ApiController@update');
// 削除
Route::get('/slack.removeToken', 'App\Http\Controllers\ApiController@destroy');