<?php

$originalTranslations = [
    'id' => 'Id',
    'restaurant' => 'Restaurant',
    'printer_id' => 'Drucker-ID',
    'status' => 'Status',
    'mac_address' => 'Mac-Adresse',
    'job_type' => 'Auftragstyp',
    'order_id' => 'Bestell-ID',
    'retries' => 'Wiederholungen',
    'printed_at' => 'Gedruckt am',
    'created_at' => 'Erstellt am',
    'options' => 'Optionen',
    'print_jobs' => 'Druckaufträge',
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