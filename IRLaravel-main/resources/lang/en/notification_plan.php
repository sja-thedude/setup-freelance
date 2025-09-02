<?php

$originalTranslations = [
    'message_created_successfully' => 'Notification plan has been created successfully.',
    'message_updated_successfully' => 'Notification plan has been updated successfully.',
    'message_saved_successfully' => 'Notification plan has been saved successfully.',
    'message_deleted_successfully' => 'Notification plan has been deleted successfully.',
    'not_found' => 'Notification plan not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
