<?php

$originalTranslations = [
    'user' => [
        'User Management' => 'Gebruikersbeheer',
        'List'            => 'Lijst',
        'View'            => 'Bekijken',
        'Add New'         => 'Toevoegen',
        'Edit'            => 'Bewerken',
        'Delete'          => 'Verwijderen',
        'Ban'             => 'Verbannen',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);