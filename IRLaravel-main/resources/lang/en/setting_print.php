<?php

$originalTranslations = [
    'message_created_successfully' => 'Setting print job has been created successfully',
    'message_updated_successfully' => 'Setting print job has been updated successfully',
    'message_deleted_successfully' => 'Setting print job has been deleted successfully',
    'message_saved_successfully' => 'Setting print job has been saved successfully',
    'not_found' => 'Setting Print not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
