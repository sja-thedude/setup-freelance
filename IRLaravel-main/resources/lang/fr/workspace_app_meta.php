<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Les métadonnées de l\'application Workspace ont été récupérées avec succès',
    'message_retrieved_successfully' => 'Les métadonnées de l\'application Workspace ont été récupérées avec succès',
    'message_created_successfully' => 'Les métadonnées de l\'application Workspace ont été créées avec succès.',
    'message_updated_successfully' => 'Les métadonnées de l\'application Workspace ont été mises à jour avec succès.',
    'message_saved_successfully' => 'Les métadonnées de l\'application Workspace ont été enregistrées avec succès.',
    'message_deleted_successfully' => 'Les métadonnées de l\'application Workspace ont été supprimées avec succès.',
    'not_found' => 'Les métadonnées de l\'application Workspace sont introuvables',

    'default_items' => [
        /* key => values */
        'reserve' => [
            'key' => 'reserve',
            'name' => 'Réserver',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'reviews' => [
            'key' => 'reviews',
            'name' => 'Avis',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'route' => [
            'key' => 'route',
            'name' => 'Itinéraire',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'jobs' => [
            'key' => 'jobs',
            'name' => 'Emplois',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'recent' => [
            'key' => 'recent',
            'name' => 'Récent',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'favorites' => [
            'key' => 'favorites',
            'name' => 'Favoris',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'account' => [
            'key' => 'account',
            'name' => 'Compte',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'share' => [
            'key' => 'share',
            'name' => 'Partager',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'loyalty' => [
            'key' => 'loyalty',
            'name' => 'Carte de fidélité',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'menu' => [
            'key' => 'menu',
            'name' => 'Menu',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
