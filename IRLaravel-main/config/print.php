<?php

return [
    'all_type' => [
        'werkbon' => 'werkbon',
        'kassabon' => 'kassabon',
        'sticker' => 'sticker',
        'a4' => 'a4'
    ],
    'job_type' => [
        'kassabon' => 0,
        'werkbon' => 1,
        'sticker' => 2
    ],
    'job_type_decode' => [
        0 => 'kassabon',
        1 => 'werkbon',
        2 => 'sticker'
    ],
    'format' => [
        'kassabon' => 'image',
        'werkbon' => 'image',
        'sticker' => env('PRINT_STICKER_TYPE', 'bbcode'), // image or bbcode
    ],
    'mm' => [
        'a4' => [
            'width' => 210,
            'height' => 297
        ],
        'sticker' => [
            'width' => 76,
            'height' => 50.8,
            'margintop' => 10, // From what point can we start printing
            'margin' => 5 // margin between stickers
        ],
        'werkbon' => [
            'width' => 80
        ],
        'kassabon' => [
            'width' => 80
        ]
    ],
    'px' => [
        'a4' => [
            'width' => 794,
            'height' => 1123
        ],
        'sticker' => [
            'width' => 547,
            'height' => 300, // normal size is 360px
            'topmargintop' => 36, // From what point can we start printing
            'margin' => 152 // margin between stickers
        ],
        'werkbon' => [
            'width' => 576
        ],
        'kassabon' => [
            'width' => 576
        ]
    ],
    'retries' => env('PRINT_RETRIES', 3),
    'debug' => env('PRINT_DEBUG', 0),
];
