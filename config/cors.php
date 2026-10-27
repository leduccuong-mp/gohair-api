<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Mobile app dùng Bearer token (Sanctum), không dùng cookie, nên
    | supports_credentials giữ false và cho phép mọi origin.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'docs/api', 'docs/api.json'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
