<?php

$originalTranslations = [
    'title' => 'Opening Hours',
    'types' => [
        'takeout' => 'Takeout',
        'delivery' => 'Delivery',
        'in_house' => 'In-house',
    ],
    'arrange_holiday' => 'Set Holiday',
    'closed' => 'Closed',
    'holiday' => 'Holiday',
    'new_holiday' => '+ New Holiday',
    'note_holiday' => 'Note',
    'updated_success' => 'Your setting has been updated successfully.',
    'updated_holiday_success' => 'Your setting has been updated successfully.',
    'holiday_none' => 'No holidays available. Set your holiday to close your store for a certain period.',
    'message_retrieved_successfully' => 'The setting has been retrieved successfully',
    'message_retrieved_list_successfully' => 'The settings have been retrieved successfully',
    'from_until' => 'FROM - UNTIL',
    'text_opening_hours' => '!!Attention!! If you change the opening hours of a weekday, the dynamic time slot settings for that day will be automatically reset!'
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);