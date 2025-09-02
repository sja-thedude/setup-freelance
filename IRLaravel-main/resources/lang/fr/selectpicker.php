<?php

$originalTranslations = [
    'noneSelectedText' => 'Rien sélectionné',
    'noneResultsText' => 'Aucun résultat pour {0}',
    'countSelectedText' => '{0} élément sélectionné',
    'countSelectedTexts' => '{0} éléments sélectionnés',
    'selectAllText' => 'Tout sélectionner',
    'deselectAllText' => 'Tout désélectionner',
    'doneButtonText' => 'Fermer',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
