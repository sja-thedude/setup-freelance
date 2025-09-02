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

    'dashboard' => 'Dashboard',
    'post'      => 'Bericht',
    'category'  => 'Categorie',
    'tag'       => 'Label',
    'page'      => 'Pagina',
    'menu'      => 'Menu Beheerder',
    'logout'    => 'Uitloggen',
    'theme'     => 'Thema Beheerder',
    'widget'    => 'Widget Beheerder',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);