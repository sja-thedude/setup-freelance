<?php

$originalTranslations = [
    'created_successfully' => 'Option item has been created',
    'updated_successfully' => 'Option item has been updated',
    'deleted_successfully' => 'Option item has been deleted',
    'message_retrieved_successfully' => 'Option item has been retrieved successfully',
    'message_retrieved_multiple_successfully' => 'Option items have been retrieved successfully',
    'not_found' => 'Option item not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
