<?php

$originalTranslations = [
    'user' => [
        'User Management' => 'Benutzerverwaltung',
        'List'            => 'Liste',
        'View'            => 'Ansehen',
        'Add New'         => 'Hinzufügen',
        'Edit'            => 'Bearbeiten',
        'Delete'          => 'Löschen',
        'Ban'             => 'Sperren',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);