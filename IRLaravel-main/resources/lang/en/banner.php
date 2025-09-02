<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Banner has been retrieved successfully.',
    'message_retrieved_list_successfully' => 'Banners have been retrieved successfully.',
    'message_created_successfully' => 'Banner has been created successfully.',
    'message_updated_successfully' => 'Banner has been updated successfully.',
    'message_saved_successfully' => 'Banner has been saved successfully.',
    'message_deleted_successfully' => 'Banner has been deleted successfully.',
    'not_found' => 'Banner not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
