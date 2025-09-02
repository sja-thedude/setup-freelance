<?php

$originalTranslations = [
    'title' => 'Mobile app',
    'description' => 'Choose your desired look and feel.',
    'message_retrieved_list_successfully' => 'Workspace apps have been retrieved successfully',
    'message_retrieved_successfully' => 'Workspace app has been retrieved successfully',
    'message_created_successfully' => 'Workspace app has been created successfully.',
    'message_updated_successfully' => 'Workspace app has been updated successfully.',
    'message_saved_successfully' => 'Workspace app has been saved successfully.',
    'message_deleted_successfully' => 'Workspace app has been deleted successfully.',
    'message_updated_status_successfully' => 'App setting status has been updated successfully.',
    'message_created_setting_successfully' => 'App setting has been created successfully.',
    'message_updated_setting_successfully' => 'App setting has been updated successfully.',
    'message_deleted_setting_successfully' => 'App setting has been deleted successfully.',
    'not_found' => 'Workspace app not found',

    'buttons' => [
        'new' => 'New'
    ],

    'theme' => [
        1 => '"Start order" in primary color',
        2 => '"Start order" in white',
        3 => 'Mobile app in dark mode',
    ],

    'settings' => [
        'description' => 'Choose your desired functions on the home screen',
        'fields' => [
            'name' => 'name',
            'title' => 'title',
            'description' => 'description',
            'content' => 'content',
            'url' => 'url',
        ],
        'placeholders' => [
            'name' => 'name',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'url' => 'URL',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
