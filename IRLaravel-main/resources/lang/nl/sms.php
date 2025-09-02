<?php

$originalTranslations = [
    'id' => 'Id',
    'restaurant' => 'Restaurant',
    "sms-count" => "SMS aantal",
    'status' => 'Status',
    'message' => 'Bericht',
    'sms' => 'Sms',
    'sent_at' => 'Verzonden op',
    'created_at' => 'Aangemaakt op',
    'to_do' => 'Te doen',
    'printing' => 'Afdrukken',
    'done' => 'Klaar',
    'error' => 'Fout',
    'kassabon' => 'Kassabon',
    'werkbon' => 'Werkbon',
    'sticker' => 'Sticker',
    'other' => 'Anders',
    'all_restaurant' => 'Alle restaurants',
    'send' => 'Verzenden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);