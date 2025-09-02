<?php

$originalTranslations = [
    'noneSelectedText' => 'Nichts ausgewählt',
    'noneResultsText' => 'Keine Ergebnisse für {0}',
    'countSelectedText' => '{0} Element ausgewählt',
    'countSelectedTexts' => '{0} Elemente ausgewählt',
    'selectAllText' => 'Alle auswählen',
    'deselectAllText' => 'Alle abwählen',
    'doneButtonText' => 'Schließen',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
