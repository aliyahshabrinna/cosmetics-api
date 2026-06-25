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
        'https://cosmetics-frontend-wfzx.vercel.app',
        'https://cosmetics-frontend-wfzx-c8pp77fjv-aliyahs-projects-32011e54.vercel.app',
    ],
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // DIUBAH JADI TRUE

];