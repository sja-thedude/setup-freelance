<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'The delivery condition has been retrieved successfully',
    'message_retrieved_list_successfully' => 'The delivery conditions have been retrieved successfully',
    'not_found' => 'Delivery condition not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);