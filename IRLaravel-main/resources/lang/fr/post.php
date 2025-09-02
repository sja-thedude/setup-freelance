<?php

$originalTranslations = [
    'manage_page' => 'Gérer la page',
    'title_post_title' => 'Titre du post',

    'btn_delete_selected_page' => 'Supprimer la page sélectionnée',
    'btn_create_page' => 'Créer une page',
    'fields' => [
        'author' => 'Auteur',
        'date' => 'Date',
        'post_title' => 'Titre du post',
        'slug' => 'Slug',
        'content' => 'Contenu',
    ],
    'placeholders' => [
        'author' => 'Auteur',
        'date' => 'Date',
        'post_title' => 'Titre du post',
        'slug' => 'Slug',
        'content' => 'Contenu',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
