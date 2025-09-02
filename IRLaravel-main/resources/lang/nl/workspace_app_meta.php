<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Werkruimte-app-metadata zijn succesvol opgehaald',
    'message_retrieved_successfully' => 'Werkruimte-app-metadata is succesvol opgehaald',
    'message_created_successfully' => 'Werkruimte-app-metadata is succesvol aangemaakt.',
    'message_updated_successfully' => 'Werkruimte-app-metadata is succesvol bijgewerkt.',
    'message_saved_successfully' => 'Werkruimte-app-metadata is succesvol opgeslagen.',
    'message_deleted_successfully' => 'Werkruimte-app-metadata is succesvol verwijderd.',
    'not_found' => 'Werkruimte-app-metadata niet gevonden',

    'default_items' => [
        /* key => values */
        'reserve' => [
            'key' => 'reserve',
            'name' => 'Reserveren',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'reviews' => [
            'key' => 'reviews',
            'name' => 'Reviews',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'route' => [
            'key' => 'route',
            'name' => 'Route',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'jobs' => [
            'key' => 'jobs',
            'name' => 'Jobs',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'recent' => [
            'key' => 'recent',
            'name' => 'Recent',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'favorites' => [
            'key' => 'favorites',
            'name' => 'Favorieten',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'account' => [
            'key' => 'account',
            'name' => 'Account',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'share' => [
            'key' => 'share',
            'name' => 'Deel',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'loyalty' => [
            'key' => 'loyalty',
            'name' => 'Klantkaart',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'menu' => [
            'key' => 'menu',
            'name' => 'Menukaart',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
