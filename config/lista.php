<?php

return [
    'ads' => [
        'enabled' => env('ADS_ENABLED', false),
        'client' => env('ADSENSE_CLIENT', 'ca-pub-xxxxxxxxxxxxxxxx'),
        'slots' => [
            'sidebar' => env('ADSENSE_SIDEBAR', '0000000000'),
            'in-content' => env('ADSENSE_INCONTENT', '0000000000'),
            'footer' => env('ADSENSE_FOOTER', '0000000000'),
            'between-list' => env('ADSENSE_BETWEEN_LIST', '0000000000'),
        ],
    ],
];
