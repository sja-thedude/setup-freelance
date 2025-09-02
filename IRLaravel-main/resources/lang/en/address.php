<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Address has been retrieved successfully.',
    'message_retrieved_list_successfully' => 'Addresses have been retrieved successfully.',
    'message_created_successfully' => 'Address has been created successfully.',
    'message_updated_successfully' => 'Address has been updated successfully.',
    'message_saved_successfully' => 'Address has been saved successfully.',
    'message_deleted_successfully' => 'Address has been deleted successfully.',
    'not_found' => 'Address not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
