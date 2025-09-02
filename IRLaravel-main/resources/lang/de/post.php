<?php

$originalTranslations = [
    'manage_page' => 'Seite verwalten',
    'title_post_title' => 'Beitragstitel',

    'btn_delete_selected_page' => 'Ausgewählte Seite löschen',
    'btn_create_page' => 'Seite erstellen',
    'fields' => [
        'author' => 'Autor',
        'date' => 'Datum',
        'post_title' => 'Beitragstitel',
        'slug' => 'Slug',
        'content' => 'Inhalt',
    ],
    'placeholders' => [
        'author' => 'Autor',
        'date' => 'Datum',
        'post_title' => 'Beitragstitel',
        'slug' => 'Slug',
        'content' => 'Inhalt',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
