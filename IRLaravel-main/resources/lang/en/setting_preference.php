<?php

$originalTranslations = [
    'message_not_found' => 'The restaurant reference settings have not been configured',
    'message_retrieved_list_successfully' => 'Setting references have been retrieved successfully',
    'message_retrieved_successfully' => 'Setting reference has been retrieved successfully',
    'message_created_successfully' => 'Setting reference has been created successfully',
    'message_updated_successfully' => 'Setting reference has been updated successfully',
    'message_deleted_successfully' => 'Setting reference has been deleted successfully',
    'message_saved_successfully' => 'Setting reference has been saved successfully',
    'not_found' => 'Setting reference not found',

    'default_items' => [
        /* key => values */
        'holiday_text' => [
            'key' => 'holiday_text',
            'name' => 'Holiday text',
            'title' => 'Display free text on home screen?',
            'content' => null,
        ],
        'table_ordering_pop_up_text' => [
            'key' => 'table_ordering_pop_up_text',
            'name' => 'Table ordering pop-up text',
            'title' => 'Display free text on home screen for on-site dining?',
            'content' => null,
        ],
        'self_ordering_pop_up_text' => [
            'key' => 'self_ordering_pop_up_text',
            'name' => 'Self-ordering pop-up text',
            'title' => 'Display free text on home screen for self-ordering?',
            'content' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);