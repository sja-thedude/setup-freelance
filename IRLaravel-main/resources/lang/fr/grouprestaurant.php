<?php

$originalTranslations = [
    'name' => 'Nom du groupe',
    'add_group' => 'Ajouter un groupe',
    'placeholder_search' => 'Rechercher un groupe',
    'deleted_confirm' => 'Le groupe a été supprimé',
    'message_retrieved_successfully' => 'Le groupe a été récupéré avec succès',
    'restaurant_list_retrieved_successfully' => 'La liste des restaurants a été récupérée avec succès',
    'not_found' => 'Le groupe n\'est pas trouvé',
    'color' => 'Couleur',

    'default_items' => [
        'name' => [
            'key' => 'name',
            'name' => null,
            'description' => null,
        ],
        'description' => [
            'key' => 'description',
            'name' => null,
            'description' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);