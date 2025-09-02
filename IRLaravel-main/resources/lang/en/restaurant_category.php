<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Restaurant category has been retrieved successfully',
    'message_retrieved_list_successfully' => 'Restaurant categories have been retrieved successfully',
    'message_created_successfully' => 'Restaurant category has been created successfully',
    'message_updated_successfully' => 'Restaurant category has been updated successfully',
    'message_deleted_successfully' => 'Restaurant category has been deleted successfully',
    'message_saved_successfully' => 'Restaurant category has been saved successfully',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
