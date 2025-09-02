<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Country has been retrieved successfully.',
    'message_retrieved_list_successfully' => 'Countries have been retrieved successfully.',
    'message_created_successfully' => 'Country has been created successfully.',
    'message_updated_successfully' => 'Country has been updated successfully.',
    'message_saved_successfully' => 'Country has been saved successfully.',
    'message_deleted_successfully' => 'Country has been deleted successfully.',
    'not_found' => 'Country not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
