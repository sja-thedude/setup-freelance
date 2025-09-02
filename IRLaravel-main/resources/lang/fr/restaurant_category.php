<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'La catégorie de restaurant a été récupérée avec succès',
    'message_retrieved_list_successfully' => 'Les catégories de restaurant ont été récupérées avec succès',
    'message_created_successfully' => 'La catégorie de restaurant a été créée avec succès',
    'message_updated_successfully' => 'La catégorie de restaurant a été mise à jour avec succès',
    'message_deleted_successfully' => 'La catégorie de restaurant a été supprimée avec succès',
    'message_saved_successfully' => 'La catégorie de restaurant a été enregistrée avec succès',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
