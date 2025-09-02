<?php

$originalTranslations = [
    'title' => 'Créneaux horaires',
    'types' => [
        0 => 'À emporter',
        1 => 'Livraison',
        2 => 'Sur place',
    ],
    'order_per_time_slot' => 'Nombre de commandes/créneau horaire',
    'max_price_per_time_slot' => 'Montant maximum/créneau horaire',
    'interval_time_slot' => 'Intervalle entre les créneaux horaires',
    'order_maximum' => 'Commander jusqu\'à un maximum de',
    'updated_success' => 'Les créneaux horaires ont été mis à jour avec succès',
    'apply_with' => 'Applicable à',
    'before_day_0' => 'Le même jour',
    'before_day_1' => '1 jour avant',
    'before_day_2' => '2 jours avant',
    'manage_dynamic_time_slot' => 'Gérer dynamiquement les créneaux horaires',
    'mini_days' => [
        '1' => 'LU',
        '2' => 'MA',
        '3' => 'ME',
        '4' => 'JE',
        '5' => 'VE',
        '6' => 'SA',
        '0' => 'DI',
    ],
    'time' => 'Temps',
    'time_mode' => 'Activé / désactivé',
    'max_best' => 'Max commandes/créneau',
    'each' => 'Chaque',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);