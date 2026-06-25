<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://cosmetics-frontend-wfzx.vercel.app', // Domain utama Vercel
    ],

    // ANTIGRAVITY: Mengizinkan semua subdomain preview bawaan vercel secara otomatis
    'allowed_origins_patterns' => [
        '/^https:\/\/cosmetics-frontend-.*\.vercel\.app$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];