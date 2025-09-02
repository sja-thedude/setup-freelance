<?php

$originalTranslations = [
    'switch_afhaal'   => 'BESTELLEN ZUR ABHOLUNG',
    'switch_levering' => 'BESTELLEN ZUR LIEFERUNG',
    'wenst_u_te'      => 'Möchten Sie als <b>Unternehmen</b>, <b>Gruppe</b> oder <b>Klasse</b> bestellen?',
    'btn_terug'       => 'Zurück',
    'example'         => 'z.B.: Rijksweg 20, 1000 Brüssel',
    'btn_bestelling'  => 'BESTELLUNG STARTEN',
    'ontdek_ons'      => 'ENTDECKEN SIE UNSER SORTIMENT',
    'home'            => 'STARTSEITE',
    'lb_mijn_adres'   => 'MEINE ADRESSE',
    'vul_hier_uw'     => 'Geben Sie hier Ihre Adresse ein',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);