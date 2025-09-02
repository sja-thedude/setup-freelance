<?php

$originalTranslations = [
    'id' => 'ID',
    'restaurant' => 'Restaurant',
    "sms-count" => "SMS Count",
    'status' => 'Status',
    'message' => 'Message',
    'sms' => 'SMS',
    'sent_at' => 'Sent At',
    'created_at' => 'Created At',
    'to_do' => 'To Do',
    'printing' => 'Printing',
    'done' => 'Done',
    'error' => 'Error',
    'kassabon' => 'Receipt',
    'werkbon' => 'Work Order',
    'sticker' => 'Sticker',
    'other' => 'Other',
    'all_restaurant' => 'All Restaurants',
    'send' => 'Send',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);