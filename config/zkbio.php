<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ZKBio Zlink Cloud API Configuration
    |--------------------------------------------------------------------------
    |
    | Your fingerprint device (e.g. MB20-VL) communicates with the ZKBio Zlink
    | cloud platform. These credentials connect your Laravel CRM to the cloud
    | via its REST API to pull employees and attendance data.
    |
    | Authentication uses AppKey + AppSecret to obtain a Tenant Token.
    |
    */

    'url'        => env('ZKBIO_ZLINK_URL', 'https://zlink-open.minervaiot.com'),
    'app_key'    => env('ZKBIO_APP_KEY'),
    'app_secret' => env('ZKBIO_APP_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Sync Settings
    |--------------------------------------------------------------------------
    |
    | token_cache_seconds: The tenant token expires in 3600 seconds (1 hour).
    |                      We cache it for 3300 seconds (~55 min) to be safe.
    |
    | page_size:           Number of records per page when fetching paginated
    |                      endpoints (max 100 per ZKBio Zlink docs).
    |
    */

    'token_cache_seconds' => 3300,
    'page_size'           => 100,

];
