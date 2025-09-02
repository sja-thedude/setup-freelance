<?php

$originalTranslations = [
    'created_successfully' => 'L\'élément d\'option a été créé',
    'updated_successfully' => 'L\'élément d\'option a été mis à jour',
    'deleted_successfully' => 'L\'élément d\'option a été supprimé',
    'message_retrieved_successfully' => 'L\'élément d\'option a été récupéré avec succès',
    'message_retrieved_multiple_successfully' => 'Les éléments d\'option ont été récupérés avec succès',
    'not_found' => 'Élément d\'option introuvable',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
