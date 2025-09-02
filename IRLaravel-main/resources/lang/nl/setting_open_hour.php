<?php

$originalTranslations = [
    'title' => 'Openingsuren',
    'types' => [
        'takeout' => 'Afhaal',
        'delivery' => 'Levering',
        'in_house' => 'Ter plaatse',
    ],
    'arrange_holiday' => 'Verlof instellen',
    'closed' => 'Gesloten',
    'holiday' => 'Verlof',
    'new_holiday' => '+ Nieuw verlof',
    'note_holiday' => 'Bericht',
    'updated_success' => 'Uw instelling is succesvol bijgewerkt.',
    'updated_holiday_success' => 'Uw instelling is succesvol bijgewerkt.',
    'holiday_none' => 'Geen verlof beschikbaar. Stel uw verlof in om uw winkel voor een bepaalde periode te sluiten.',
    'message_retrieved_successfully' => 'De instelling is succesvol opgehaald.',
    'message_retrieved_list_successfully' => 'De instellingen zijn succesvol opgehaald.',
    'from_until' => 'VAN - TOT EN MET',
    'text_opening_hours' => '!!Opgelet!! Indien u de openingsuren van een weekdag wijzigt, dan zullen de dynamische tijdslot instellingen voor deze dag automatisch resetten!'
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);