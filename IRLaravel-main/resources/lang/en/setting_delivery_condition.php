<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Setting has been retrieved successfully.',
    'message_retrieved_list_successfully' => 'Settings have been retrieved successfully.',
    'message_created_successfully' => 'Setting has been created successfully.',
    'message_updated_successfully' => 'Setting has been updated successfully.',
    'message_saved_successfully' => 'Setting has been saved successfully.',
    'message_deleted_successfully' => 'Setting has been deleted successfully.',
    'not_found' => 'Setting not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
