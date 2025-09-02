<?php

$originalTranslations = [
    'user' => [
        'User Management' => 'User Management',
        'List'            => 'List',
        'View'            => 'View',
        'Add New'         => 'Add New',
        'Edit'            => 'Edit',
        'Delete'          => 'Delete',
        'Ban'             => 'Ban',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);