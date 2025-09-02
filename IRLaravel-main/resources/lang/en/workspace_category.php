<?php

$originalTranslations = [
    'message_created_successfully' => 'Workspace category has been created successfully.',
    'message_updated_successfully' => 'Workspace category has been updated successfully.',
    'message_saved_successfully' => 'Workspace category has been saved successfully.',
    'message_deleted_successfully' => 'Workspace category has been deleted successfully.',
    'not_found' => 'Workspace category not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
