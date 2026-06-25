<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    // ANTIGRAVITY: Mengizinkan semua subdomain preview bawaan vercel secara otomatis
    'allowed_origins_patterns' => [
        '/^https:\/\/cosmetics-frontend-.*\.vercel\.app$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];