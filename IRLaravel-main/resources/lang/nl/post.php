<?php

$originalTranslations = [
    'manage_page' => 'Pagina beheren',
    'title_post_title' => 'Titel bericht',

    'btn_delete_selected_page' => 'Geselecteerde pagina verwijderen',
    'btn_create_page' => 'Pagina aanmaken',
    'fields' => [
        'author' => 'Auteur',
        'date' => 'Datum',
        'post_title' => 'Titel bericht',
        'slug' => 'Slug',
        'content' => 'Inhoud',
    ],
    'placeholders' => [
        'author' => 'Auteur',
        'date' => 'Datum',
        'post_title' => 'Titel bericht',
        'slug' => 'Slug',
        'content' => 'Inhoud',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
