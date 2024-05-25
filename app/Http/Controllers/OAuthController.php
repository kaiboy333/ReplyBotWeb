<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class OAuthController extends Controller
{
    public function index(Request $request)
    {
        return view('index');
    }

    public function auth(Request $request)
    {
        Log::info("request: ${request}");

        $code = $request->input('code');
        $state = $request->input('state');

        Log::info("code: ${code}");
        Log::info("state: ${state}");

        if ($request->filled('error'))
        {
            return response('slack returned error response.', 500);
        }

        $client_id = config('slack.client_id');
        $client_secret = config('slack.client_secret');

        Log::info("client_id: ${client_id}");
        Log::info("client_secret: ${client_secret}");

        $oauth_access_api_uri = config('slack.oauth_access_api_uri');
        Log::info("oauth_access_api_uri: ${oauth_access_api_uri}");

        $client = new \GuzzleHttp\Client();

        // Slack APIのoauth.accessを呼び出してトークンを削除する
        $oauth_response = $client->request('POST', $oauth_access_api_uri, [
            'form_params' => [
                'code' => $code,
                'client_id' => $client_id,
                'client_secret' => $client_secret,    
            ]
        ]);
        $body_str = $oauth_response->getBody();
        Log::info("body_str: ${body_str}");

        $body = json_decode($body_str, true);

        if (!$body['ok'])
        {
            return response($body['error'], 500);
        }

        $authed_user = $body['authed_user'];
        $user_id = $authed_user['id'];
        $access_token = $authed_user['access_token'];

        Log::info("user_id: ${user_id}");
        Log::info("access_token: ${access_token}");

        // user_idでレコードを検索する
        $user = User::where('user_id', $user_id)->first();

        if ($user) {
            Log::info('user_id already resisted');

            $old_access_token = $user->access_token;
            Log::info("old_access_token: ${old_access_token}");

            if ($old_access_token != $access_token)
            {
                // 新しいトークンをデータベースに置き換えて保存する
                $user->access_token = $access_token;
                $user->save();
                
                // このままだと残ってしまうので、トークンを削除する
                $auth_revoke_api_uri = config('slack.auth_revoke_api_uri');
                $revoke_response = $client->request('POST', $auth_revoke_api_uri, [
                    'form_params' => [
                        'token' => $old_access_token,
                    ]
                ]);
                $body_str = $revoke_response->getBody();
                Log::info("body_str: ${body_str}");

                $body = json_decode($body_str, true);

                if (!$body['ok'])
                {
                    return response($body['error'], 500);
                }

                Log::info('deleted old access token');
            }
            else
            {
                Log::info('old is same access token');
                return response('old is same access token', 200);
            }
        }
        else
        {
            // レコードが存在しない場合は、新しいレコードを作成
            $user = new User();
            $user->user_id = $user_id;
            $user->access_token = $access_token;
            $user->save();
        }

        Log::info('resisted access token');

        return view('auth');
    }
}
