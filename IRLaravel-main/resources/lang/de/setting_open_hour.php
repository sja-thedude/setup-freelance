<?php

$originalTranslations = [
    'title' => 'Öffnungszeiten',
    'types' => [
        'takeout' => 'Mitnahme',
        'delivery' => 'Lieferung',
        'in_house' => 'Im Haus',
    ],
    'arrange_holiday' => 'Urlaub einstellen',
    'closed' => 'geschlossen',
    'holiday' => 'Urlaub',
    'new_holiday' => '+ Neuer Urlaub',
    'note_holiday' => 'Nachricht',
    'updated_success' => 'Ihre Einstellungen wurden erfolgreich aktualisiert.',
    'updated_holiday_success' => 'Ihre Einstellungen wurden erfolgreich aktualisiert.',
    'holiday_none' => 'Kein Urlaub verfügbar. Stellen Sie Ihren Urlaub ein, um Ihr Geschäft für einen bestimmten Zeitraum zu schließen.',
    'message_retrieved_successfully' => 'Die Einstellungen wurden erfolgreich abgerufen.',
    'message_retrieved_list_successfully' => 'Die Einstellungen wurden erfolgreich abgerufen.',
    'from_until' => 'VON - BIS',
    'text_opening_hours' => '!!Achtung!! Wenn Sie die Öffnungszeiten eines Wochentags ändern, werden die dynamischen Zeitschloteinstellungen für diesen Tag automatisch zurückgesetzt!'
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);