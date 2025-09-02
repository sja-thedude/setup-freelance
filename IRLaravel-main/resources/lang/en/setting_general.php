<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Setting timeslots have been retrieved successfully',
    'message_retrieved_successfully' => 'Setting timeslot has been retrieved successfully',
    'message_created_successfully' => 'Setting timeslot has been created successfully',
    'message_updated_successfully' => 'Setting timeslot has been updated successfully',
    'message_deleted_successfully' => 'Setting timeslot has been deleted successfully',
    'message_saved_successfully' => 'Setting timeslot has been saved successfully',
    'not_found' => 'Setting Timeslot not found',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
