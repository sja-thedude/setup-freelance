<?php

$originalTranslations = [
    'take_out' => 'À emporter',
    'delivery' => 'Livraison', 
    'in_house' => 'Sur place',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);