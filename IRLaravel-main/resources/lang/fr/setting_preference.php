<?php

$originalTranslations = [
    'message_not_found' => 'Les paramètres de référence du restaurant n\'ont pas été configurés',
    'message_retrieved_list_successfully' => 'Les références de paramètres ont été récupérées avec succès',
    'message_retrieved_successfully' => 'La référence de paramètre a été récupérée avec succès',
    'message_created_successfully' => 'La référence de paramètre a été créée avec succès',
    'message_updated_successfully' => 'La référence de paramètre a été mise à jour avec succès',
    'message_deleted_successfully' => 'La référence de paramètre a été supprimée avec succès',
    'message_saved_successfully' => 'La référence de paramètre a été enregistrée avec succès',
    'not_found' => 'Référence de paramètre non trouvée',

    'default_items' => [
        /* key => values */
        'holiday_text' => [
            'key' => 'holiday_text',
            'name' => 'Texte des vacances',
            'title' => 'Afficher le texte libre sur l\'écran d\'accueil?',
            'content' => null,
        ],
        'table_ordering_pop_up_text' => [
            'key' => 'table_ordering_pop_up_text',
            'name' => 'Texte de la fenêtre contextuelle de commande à table',
            'title' => 'Afficher le texte libre sur l\'écran d\'accueil pour les commandes sur place?',
            'content' => null,
        ],
        'self_ordering_pop_up_text' => [
            'key' => 'self_ordering_pop_up_text',
            'name' => 'Texte de la fenêtre contextuelle de commande autonome',
            'title' => 'Afficher le texte libre sur l\'écran d\'accueil pour les commandes à la caisse?',
            'content' => null,
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);