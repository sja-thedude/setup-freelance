<?php

$originalTranslations = [
    'message_invalid_token' => 'Restaurant was not setup payment token',
    'message_retrieved_list_successfully' => 'Setting payments have been retrieved successfully',
    'message_retrieved_successfully' => 'Setting payment has been retrieved successfully',
    'message_created_successfully' => 'Setting payment has been created successfully',
    'message_updated_successfully' => 'Setting payment has been updated successfully',
    'message_deleted_successfully' => 'Setting payment has been deleted successfully',
    'message_saved_successfully' => 'Setting payment has been saved successfully',
    'not_found' => 'Setting payment not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);