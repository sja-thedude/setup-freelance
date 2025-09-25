<?php

return [

    'driver' => env('MAIL_MAILER', 'smtp'),

    'host' => env('MAIL_HOST', 'smtp.postmarkapp.com'),

    'port' => env('MAIL_PORT', 2525),

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'admin@itsready.be'),
        'name' => env('MAIL_FROM_NAME', 'itsreadydev'),
    ],

    'encryption' => env('MAIL_ENCRYPTION', null),

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    'sendmail' => '/usr/sbin/sendmail -bs',

    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    'stream' => [
        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ],

];