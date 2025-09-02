<?php

return [
    'global' => [
        // How many hours before an order happens it will be printed? Orders from the same day will be printed immediately.
        'order_scan_interval_hours' => env('CONNECTORS_GLOBAL_SCAN_ORDERS_INTERVAL_HOURS', 4),
    ],

    'hendrickx_kassas' => [
        'verbose_level' => env('CONNECTORS_HENDRICKX_KASSAS_VERBOSE_LEVEL', 0),
        'init_vector' => env('CONNECTORS_HENDRICKX_KASSAS_INIT_VECTOR', "\0\0\0\0\0\0\0\0"),
        'encryption' => env('CONNECTORS_HENDRICKX_KASSAS_ENCRYPTION', true),
    ]
];