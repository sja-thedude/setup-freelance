<?php

$originalTranslations = [
    'take_out' => 'Abholung',
    'delivery' => 'Lieferung',
    'in_house' => 'Vor Ort',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);