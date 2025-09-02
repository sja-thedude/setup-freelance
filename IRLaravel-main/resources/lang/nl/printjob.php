<?php

$originalTranslations = [
    'id' => 'Id',
    'restaurant' => 'Restaurant',
    'printer_id' => 'Printer-id',
    'status' => 'Status',
    'mac_address' => 'Mac-adres',
    'job_type' => 'Taaktype',
    'order_id' => 'Bestel-id',
    'retries' => 'Pogingen',
    'printed_at' => 'Geprint op',
    'created_at' => 'Aangemaakt op',
    'options' => 'Opties',
    'print_jobs' => 'Printopdrachten',
    'to_do' => 'Te doen',
    'printing' => 'Afdrukken',
    'done' => 'Gedaan',
    'error' => 'Fout',
    'kassabon' => 'Kassabon',
    'werkbon' => 'Werkbon',
    'sticker' => 'Sticker',
    'other' => 'Anders',
    'all_restaurant' => 'Alle handelaars',
    'send' => 'Verzenden',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);