<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        $params = [
            'form_params' => [
                'client_id' => config('slack.client_id'),
                'client_secret' => config('slack.client_secret'),
                'code' => $code,
                'redirect_uri' => config('redirect_uri'),    
            ]
        ];

        Log::info("client_id: ${client_id}");
        Log::info("client_secret: ${client_secret}");
        Log::info("redirect_uri: ${redirect_uri}");

        $oauth_access_api_uri = config('slack.oauth_access_api_uri');
        Log::info("oauth_access_api_uri: ${oauth_access_api_uri}");

        // HTTP POSTリクエスト
        $oauth_response = Http::post($oauth_access_api_uri, $params);
        $body = json_decode($oauth_response->getBody(), true);

        if (!$body['ok'])
        {
            return response('oauth returned error response.', 500);
        }

        $access_token = $body['access_token'];
        $user_id = $body['authed_user']['id'];

        Log::info("access_token: ${access_token}");
        Log::info("user_id: ${user_id}");

        return "ok";
    }
}
