<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'City has been retrieved successfully.',
    'message_retrieved_list_successfully' => 'Cities have been retrieved successfully.',
    'message_created_successfully' => 'City has been created successfully.',
    'message_updated_successfully' => 'City has been updated successfully.',
    'message_saved_successfully' => 'City has been saved successfully.',
    'message_deleted_successfully' => 'City has been deleted successfully.',
    'not_found' => 'City not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
