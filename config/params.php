<?php

return [
    'adminEmail' => 'admin@example.com',
    'trackers' =>
    [
        'trackingmore' => [
            'url' => 'http://api.trackingmore.com/v2',
            //'url' => 'http://localhost:5555', // Testing purpose
            'token' => 'XXX',

            'limits' => [  // Limit trackings in one round by operations
                'detect' => 100,
                'register' => 500,
                'check' => 100,
                'cleanup' => 100,
            ],
            'pause' => 0, // Pause in sec between requests
            'holdoff' => 120, // Pause in sec on "too many requests"
        ],
    ],
];
