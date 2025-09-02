<?php

$originalTranslations = [
    'user' => [
        'User Management' => 'Gestion des utilisateurs',
        'List'            => 'Liste',
        'View'            => 'Voir',
        'Add New'         => 'Ajouter',
        'Edit'            => 'Modifier',
        'Delete'          => 'Supprimer',
        'Ban'             => 'Bannir',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);