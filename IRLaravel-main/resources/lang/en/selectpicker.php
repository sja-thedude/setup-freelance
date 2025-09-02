<?php

$originalTranslations = [
    'noneSelectedText' => 'Nothing selected',
    'noneResultsText' => 'No results matched {0}',
    'countSelectedText' => '{0} item selected',
    'countSelectedTexts' => '{0} items selected',
    'selectAllText' => 'Select All',
    'deselectAllText' => 'Deselect All',
    'doneButtonText' => 'Close',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
