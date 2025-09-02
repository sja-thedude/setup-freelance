<?php

$originalTranslations = [
    'switch_afhaal'   => 'COMMANDER À EMPORTER',
    'switch_levering' => 'COMMANDER EN LIVRAISON',
    'wenst_u_te'      => 'Souhaitez-vous commander en tant qu\'<b>entreprise</b>, <b>groupe</b> ou <b>classe</b>?',
    'btn_terug'       => 'Retour',
    'example'         => 'ex.: Rue de la Loi 20, 1000 Bruxelles',
    'btn_bestelling'  => 'COMMENCER LA COMMANDE',
    'ontdek_ons'      => 'DÉCOUVREZ NOTRE ASSORTIMENT',
    'home'            => 'ACCUEIL',
    'lb_mijn_adres'   => 'MON ADRESSE',
    'vul_hier_uw'     => 'Entrez votre adresse ici',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);