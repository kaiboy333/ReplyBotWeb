<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // HTTP POSTリクエスト
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
        $access_token = $authed_user['access_token'];
        $user_id = $authed_user['id'];
        $team_id = $body['team']['id'];

        Log::info("access_token: ${access_token}");
        Log::info("user_id: ${user_id}");

        return "ok";
    }
}
