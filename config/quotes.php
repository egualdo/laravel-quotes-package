<?php

return [
    'api' => [
        'base_url' => env('QUOTES_API_URL', 'https://dummyjson.com/quotes'),
        'timeout' => env('QUOTES_API_TIMEOUT', 10),
    ],

    'rate_limiting' => [
        'request_limit' => env('QUOTES_REQUEST_LIMIT', 5),
        'time_window' => env('QUOTES_TIME_WINDOW', 30),
    ],

    'cache' => [
        'key' => env('QUOTES_CACHE_KEY', 'quotes_storage'),
        'ttl' => env('QUOTES_CACHE_TTL', 3600),
    ],

    'pagination' => [
        'per_page' => env('QUOTES_PER_PAGE', 10),
    ],
];
