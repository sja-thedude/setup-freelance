<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Workspace app metas have been retrieved successfully',
    'message_retrieved_successfully' => 'Workspace app meta has been retrieved successfully',
    'message_created_successfully' => 'Workspace app meta has been created successfully.',
    'message_updated_successfully' => 'Workspace app meta has been updated successfully.',
    'message_saved_successfully' => 'Workspace app meta has been saved successfully.',
    'message_deleted_successfully' => 'Workspace app meta has been deleted successfully.',
    'not_found' => 'Workspace app meta not found',

    'default_items' => [
        /* key => values */
        'reserve' => [
            'key' => 'reserve',
            'name' => 'Reserve',
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
            'name' => 'Favorites',
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
            'name' => 'Share',
            'title' => null,
            'description' => null,
            'content' => null,
            'url' => null,
        ],
        'loyalty' => [
            'key' => 'loyalty',
            'name' => 'Loyalty Card',
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
