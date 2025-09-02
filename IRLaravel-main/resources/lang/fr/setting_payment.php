<?php

$originalTranslations = [
    'message_invalid_token' => 'Le restaurant n\'a pas configuré le jeton de paiement',
    'message_retrieved_list_successfully' => 'Les paramètres de paiement ont été récupérés avec succès',
    'message_retrieved_successfully' => 'Le paramètre de paiement a été récupéré avec succès',
    'message_created_successfully' => 'Le paramètre de paiement a été créé avec succès',
    'message_updated_successfully' => 'Le paramètre de paiement a été mis à jour avec succès',
    'message_deleted_successfully' => 'Le paramètre de paiement a été supprimé avec succès',
    'message_saved_successfully' => 'Le paramètre de paiement a été enregistré avec succès',
    'not_found' => 'Paramètre de paiement non trouvé',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);