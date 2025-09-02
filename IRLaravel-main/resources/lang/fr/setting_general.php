<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Les créneaux horaires de réglage ont été récupérés avec succès',
    'message_retrieved_successfully' => 'Le créneau horaire de réglage a été récupéré avec succès',
    'message_created_successfully' => 'Le créneau horaire de réglage a été créé avec succès',
    'message_updated_successfully' => 'Le créneau horaire de réglage a été mis à jour avec succès',
    'message_deleted_successfully' => 'Le créneau horaire de réglage a été supprimé avec succès',
    'message_saved_successfully' => 'Le créneau horaire de réglage a été enregistré avec succès',
    'not_found' => 'Créneau horaire de réglage non trouvé',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
