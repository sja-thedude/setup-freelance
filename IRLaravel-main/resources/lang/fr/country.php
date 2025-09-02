<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Le pays a été récupéré avec succès.',
    'message_retrieved_list_successfully' => 'Les pays ont été créés avec succès.',
    'message_created_successfully' => 'Le pays a été créé avec succès.',
    'message_updated_successfully' => 'Le pays a été mis à jour avec succès.',
    'message_saved_successfully' => 'Le pays a été enregistré avec succès.',
    'message_deleted_successfully' => 'Le pays a été supprimé avec succès.',
    'not_found' => 'Pays non trouvé',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
