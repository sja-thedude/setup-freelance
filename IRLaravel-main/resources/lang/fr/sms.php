<?php

$originalTranslations = [
    'id' => 'Id',
    'restaurant' => 'Restaurant',
    "sms-count" => "Nombre de SMS",
    'status' => 'Statut',
    'message' => 'Message',
    'sms' => 'Sms',
    'sent_at' => 'Envoyé à',
    'created_at' => 'Créé à',
    'to_do' => 'À faire',
    'printing' => 'Impression',
    'done' => 'Fait',
    'error' => 'Erreur',
    'kassabon' => 'Ticket de caisse',
    'werkbon' => 'Bon de travail',
    'sticker' => 'Autocollant',
    'other' => 'Autre',
    'all_restaurant' => 'Tous les restaurants',
    'send' => 'Envoyer',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);