<?php

$originalTranslations = [

    /*
    |--------------------------------------------------------------------------
    | Default Simple CMS
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'dashboard' => 'Armaturenbrett',
    'post'      => 'Beitrag',
    'category'  => 'Kategorie',
    'tag'       => 'Stichwort',
    'page'      => 'Seite',
    'menu'      => 'Menü-Manager',
    'logout'    => 'Abmelden',
    'theme'     => 'Themen-Manager',
    'widget'    => 'Widget-Manager',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);