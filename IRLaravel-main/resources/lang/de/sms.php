<?php

$originalTranslations = [
    'id' => 'Id',
    'restaurant' => 'Restaurant',
    "sms-count" => "SMS-Anzahl",
    'status' => 'Status',
    'message' => 'Nachricht',
    'sms' => 'SMS',
    'sent_at' => 'Gesendet am',
    'created_at' => 'Erstellt am',
    'to_do' => 'Zu erledigen',
    'printing' => 'Drucken',
    'done' => 'Erledigt',
    'error' => 'Fehler',
    'kassabon' => 'Kassenbon',
    'werkbon' => 'Arbeitsauftrag',
    'sticker' => 'Aufkleber',
    'other' => 'Andere',
    'all_restaurant' => 'Alle Restaurants',
    'send' => 'Senden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);