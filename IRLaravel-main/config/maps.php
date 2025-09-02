<?php

return [
    'api_key' => env('MAPS_API_KEY', 'AIzaSyCdRp4DqipireUuPwrLjh_BYxV73L21HWM'),
    'countries' => [
        'fr' => [
            'center' => ['lat' => 46.2, 'lng' => 2.2],
            'zoom' => 5,
            'code' => 'fr'
        ],
        'de' => [
            'center' => ['lat' => 51.53526, 'lng' => 7.29693],
            'zoom' => 5,
            'code' => 'de'
        ],
        'nl' => [
            'center' => ['lat' => 52.36110, 'lng' => 4.89391],
            'zoom' => 5,
            'code' => 'nl'
        ],
        'en' => [
            'center' => ['lat' => 51.5142, 'lng' => -0.08477],
            'zoom' => 5,
            'code' => 'uk'
        ],

    ]
];
