<?php

return [
    'id' => env('LOGIN_MODULE_CLIENT_ID'),
    'secret' => env('LOGIN_MODULE_SECRET'),
    'base_url' => env('LOGIN_MODULE_URL'),
    'redirect_uri' => env('APP_URL').'/oauth_callback/login'
];