<?php

$originalTranslations = [
    'singular' => 'Kundenkarte',

    'message_retrieved_successfully' => 'Die Treue wurde erfolgreich abgerufen',
    'message_retrieved_multiple_successfully' => 'Die Treueprogramme wurden erfolgreich abgerufen',
    'message_created_successfully' => 'Die Treue wurde erfolgreich erstellt',
    'message_updated_successfully' => 'Die Treue wurde erfolgreich aktualisiert',
    'message_saved_successfully' => 'Die Treue wurde erfolgreich gespeichert',
    'message_deleted_successfully' => 'Die Treue wurde erfolgreich gelöscht',
    'message_redeem_successfully' => 'Sie haben erfolgreich eingelöst',
    'message_already_redeem' => 'Sie haben diesen Rabatt bereits eingelöst! Bestellen Sie und nutzen Sie diesen Rabatt!',
    'message_not_enough_point' => ':point Credits benötigt',
    'message_unable_re_redeemable' => 'Sie haben bereits einen anderen Rabatt eingelöst! Nutzen Sie diesen, bevor Sie einen neuen einlösen!',
    'message_redeem_retrieved_successfully' => 'Der Einlösevorgang wurde erfolgreich abgerufen',
    'not_found' => 'Treue nicht gefunden',

    'text_credits' => ':number CREDITS',

    'button_redeem' => 'Einlösen',
    'already_redeem' => 'Bereits eingelöst',
    'not_use_loyalty_card' => ':restaurant_name verwendet keine Kundenkarte',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
