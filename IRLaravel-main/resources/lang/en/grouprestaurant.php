<?php

$originalTranslations = [
    'name' => 'Group name',
    'add_group' => 'Add group',
    'placeholder_search' => 'Search group',
    'deleted_confirm' => 'The group has been deleted',
    'message_retrieved_successfully' => 'The group has been retrieved successfully',
    'restaurant_list_retrieved_successfully' => 'The restaurant list has been retrieved successfully',
    'not_found' => 'Group is not found',
    'color' => 'Color',

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