<?php

$originalTranslations = [
    'id' => 'Id',
    'restaurant' => 'Restaurant',
    'printer_id' => 'Id de l\'imprimante',
    'status' => 'Statut',
    'mac_address' => 'Adresse MAC',
    'job_type' => 'Type de travail',
    'order_id' => 'Id de la commande',
    'retries' => 'Tentatives',
    'printed_at' => 'Imprimé le',
    'created_at' => 'Créé le',
    'options' => 'Options',
    'print_jobs' => 'Travaux d\'impression',
    'to_do' => 'À faire',
    'printing' => 'En cours d\'impression',
    'done' => 'Terminé',
    'error' => 'Erreur',
    'kassabon' => 'Ticket de caisse',
    'werkbon' => 'Bon de travail',
    'sticker' => 'Étiquette',
    'other' => 'Autre',
    'all_restaurant' => 'Tous les restaurants',
    'send' => 'Envoyer',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);