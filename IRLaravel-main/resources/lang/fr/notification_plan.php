<?php

$originalTranslations = [
    'message_created_successfully' => 'Le plan de notification a été créé avec succès.',
    'message_updated_successfully' => 'Le plan de notification a été mis à jour avec succès.',
    'message_saved_successfully' => 'Le plan de notification a été enregistré avec succès.',
    'message_deleted_successfully' => 'Le plan de notification a été supprimé avec succès.',
    'not_found' => 'Plan de notification introuvable',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
