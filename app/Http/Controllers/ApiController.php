<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request || !$request->password)
        {
            return [
                'ok' => false,
                'error' => 'fewer parameters.',
            ];
        }

        if ($request->password != config('slack.password'))
        {
            return [
                'ok' => false,
                'error' => 'not correct password.',
            ];
        }

        $users = User::all();
        return [
            'ok' => true,
            'users' => $users,
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request || !$request->user_id || !$request->access_token || !$request->password)
        {
            return [
                'ok' => false,
                'error' => 'fewer parameters.',
            ];
        }

        if ($request->password != config('slack.password'))
        {
            return [
                'ok' => false,
                'error' => 'not correct password.',
            ];
        }

        // idでレコードを検索する
        $user = User::where('user_id', $request->user_id)->first();

        // 既にあったら
        if ($user)
        {
            return [
                'ok' => false,
                'error' => 'user already exists.',
            ];
        }        

        $user = new User();
        $user->user_id = $request->user_id;
        $user->access_token = $request->access_token;
        $user->save();

        // Route::getのnameがshowであるものにリダイレクト
        return redirect()->route('show', ['user_id' => $request->user_id, 'password' => $request->password]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        if (!$request || !$request->user_id || !$request->password)
        {
            return [
                'ok' => false,
                'error' => 'fewer parameters.',
            ];
        }

        if ($request->password != config('slack.password'))
        {
            return [
                'ok' => false,
                'error' => 'not correct password.',
            ];
        }

        // idでレコードを検索する
        $user = User::where('user_id', $request->user_id)->first();

        if ($user)
        {
            return [
                'ok' => true,
                'user' => $user,
            ];
        }

        return [
            'ok' => false,
            'error' => 'user not found.',
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if (!$request || !$request->user_id || !$request->access_token || !$request->password)
        {
            return [
                'ok' => false,
                'error' => 'fewer parameters.',
            ];
        }

        if ($request->password != config('slack.password'))
        {
            return [
                'ok' => false,
                'error' => 'not correct password.',
            ];
        }

        // idでレコードを検索する
        $user = User::where('user_id', $request->user_id)->first();

        if ($user)
        {
            $user->access_token = $request->access_token;
            $user->save();
        }

        // Route::getのnameがshowであるものにリダイレクト
        return redirect()->route('show', ['user_id' => $request->user_id, 'password' => $request->password]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (!$request || !$request->user_id || !$request->password)
        {
            return [
                'ok' => false,
                'error' => 'fewer parameters.',
            ];
        }

        if ($request->password != config('slack.password'))
        {
            return [
                'ok' => false,
                'error' => 'not correct password.',
            ];
        }

        // idでレコードを検索する
        $user = User::where('user_id', $request->user_id)->first();

        if (!$user)
        {
            return [
                'ok' => false,
                'error' => 'user not found',
            ];
        }

        $user->delete();
        $user = User::where('user_id', $request->user_id)->first();

        if (!$user)
        {
            return [
                'ok' => true,
            ];
        }
        else
        {
            return [
                'ok' => false,
                'error' => 'failed to delete',
            ];
        }
    }
}
