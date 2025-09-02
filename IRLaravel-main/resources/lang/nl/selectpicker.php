<?php

$originalTranslations = [
    'noneSelectedText' => 'Niets geselecteerd',
    'noneResultsText' => 'Geen resultaten voor {0}',
    'countSelectedText' => '{0} item geselecteerd',
    'countSelectedTexts' => '{0} items geselecteerd',
    'selectAllText' => 'Alles selecteren',
    'deselectAllText' => 'Alles deselecteren',
    'doneButtonText' => 'Sluiten',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
