<?php

return [
    'zfr_cors' => [
        'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
        'allowed_origins' => ['*'],
        'allowed_headers' => [
            'Authorization', 'X-Requested-With', 'Content-Type'
        ],
        'max_age' => 120,
        'allowed_credentials' => true,
    ],
];
