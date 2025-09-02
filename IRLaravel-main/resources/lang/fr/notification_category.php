<?php

$originalTranslations = [
    'message_created_successfully' => 'La catégorie de notification a été créée avec succès.',
    'message_updated_successfully' => 'La catégorie de notification a été mise à jour avec succès.',
    'message_saved_successfully' => 'La catégorie de notification a été enregistrée avec succès.',
    'message_deleted_successfully' => 'La catégorie de notification a été supprimée avec succès.',
    'not_found' => 'Catégorie de notification introuvable',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
