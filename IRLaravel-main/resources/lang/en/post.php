<?php

$originalTranslations = [
    'manage_page' => 'Manage Page',
    'title_post_title' => 'Post Title',

    'btn_delete_selected_page' => 'Delete Selected Page',
    'btn_create_page' => 'Create Page',
    'fields' => [
        'author' => 'Author',
        'date' => 'Date',
        'post_title' => 'Title Post',
        'slug' => 'Slug',
        'content' => 'Content',
    ],
    'placeholders' => [
        'author' => 'Author',
        'date' => 'Date',
        'post_title' => 'Title Post',
        'slug' => 'Slug',
        'content' => 'Content',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
