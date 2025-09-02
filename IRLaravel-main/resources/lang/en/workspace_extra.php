<?php

$originalTranslations = [
    'message_created_successfully' => 'Workspace extra has been created successfully.',
    'message_updated_successfully' => 'Workspace extra has been updated successfully.',
    'message_saved_successfully' => 'Workspace extra has been saved successfully.',
    'message_deleted_successfully' => 'Workspace extra has been deleted successfully.',
    'not_found' => 'Workspace extra not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
