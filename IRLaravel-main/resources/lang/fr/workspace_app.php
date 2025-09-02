<?php

$originalTranslations = [
    'title' => 'Application mobile',
    'description' => 'Choisissez votre apparence souhaitée.',
    'message_retrieved_list_successfully' => 'Les applications de l\'espace de travail ont été récupérées avec succès',
    'message_retrieved_successfully' => 'L\'application de l\'espace de travail a été récupérée avec succès',
    'message_created_successfully' => 'L\'application de l\'espace de travail a été créée avec succès.',
    'message_updated_successfully' => 'L\'application de l\'espace de travail a été mise à jour avec succès.',
    'message_saved_successfully' => 'L\'application de l\'espace de travail a été enregistrée avec succès.',
    'message_deleted_successfully' => 'L\'application de l\'espace de travail a été supprimée avec succès.',
    'message_updated_status_successfully' => 'Le statut des paramètres de l\'application a été mis à jour avec succès.',
    'message_created_setting_successfully' => 'Les paramètres de l\'application ont été créés avec succès.',
    'message_updated_setting_successfully' => 'Les paramètres de l\'application ont été mis à jour avec succès.',
    'message_deleted_setting_successfully' => 'Les paramètres de l\'application ont été supprimés avec succès.',
    'not_found' => 'Application de l\'espace de travail non trouvée',

    'buttons' => [
        'new' => 'Nouveau'
    ],

    'theme' => [
        1 => '"Commencer la commande" en couleur primaire',
        2 => '"Commencer la commande" en blanc',
        3 => 'Application mobile en mode sombre',
    ],

    'settings' => [
        'description' => 'Choisissez vos fonctionnalités souhaitées sur l\'écran d\'accueil',
        'fields' => [
            'name' => 'nom',
            'title' => 'titre',
            'description' => 'description',
            'content' => 'contenu',
            'url' => 'url',
        ],
        'placeholders' => [
            'name' => 'nom',
            'title' => 'Titre',
            'description' => 'Description',
            'content' => 'Contenu',
            'url' => 'URL',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
