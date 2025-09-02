<?php

$originalTranslations = [
    'message_sent_successfully' => 'Message sent successfully. We will contact you as soon as possible.',
    'message_created_successfully' => 'Workspace app has been created successfully.',
    'message_updated_successfully' => 'Workspace app has been updated successfully.',
    'message_saved_successfully' => 'Workspace app has been saved successfully.',
    'message_deleted_successfully' => 'Workspace app has been deleted successfully.',
    'not_found' => 'Workspace app not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);