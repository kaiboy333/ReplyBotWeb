<?php
return [
    'client_id' => env('SLACK_CLIENT_ID'),
    'client_secret' => env('SLACK_CLIENT_SECRET'),
    'oauth_access_api_uri' => 'https://slack.com/api/oauth.v2.access',
    'auth_revoke_api_uri' => 'https://slack.com/api/auth.revoke',
    'password' => env('SLACK_PASSWORD'),
];