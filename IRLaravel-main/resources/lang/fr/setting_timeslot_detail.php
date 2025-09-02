<?php

$originalTranslations = [
    'message_retrieved_list_successfully' => 'Les détails du créneau horaire ont été récupérés avec succès',
    'message_retrieved_successfully' => 'Le détail du créneau horaire a été récupéré avec succès',
    'message_created_successfully' => 'Le détail du créneau horaire a été créé avec succès',
    'message_updated_successfully' => 'Le détail du créneau horaire a été mis à jour avec succès',
    'message_deleted_successfully' => 'Le détail du créneau horaire a été supprimé avec succès',
    'message_saved_successfully' => 'Le détail du créneau horaire a été enregistré avec succès',
    'not_found' => 'Détail du créneau horaire non trouvé',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
