<?php

$originalTranslations = [
    'take_out' => 'Take Out',
    'delivery' => 'Delivery',
    'in_house' => 'In House',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);