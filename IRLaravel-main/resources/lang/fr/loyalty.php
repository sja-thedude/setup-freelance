<?php

$originalTranslations = [
    'singular' => 'Carte de fidélité',

    'message_retrieved_successfully' => 'La carte de fidélité a été récupérée avec succès',
    'message_retrieved_multiple_successfully' => 'Les cartes de fidélité ont été récupérées avec succès',
    'message_created_successfully' => 'La carte de fidélité a été créée avec succès',
    'message_updated_successfully' => 'La carte de fidélité a été mise à jour avec succès',
    'message_saved_successfully' => 'La carte de fidélité a été enregistrée avec succès',
    'message_deleted_successfully' => 'La carte de fidélité a été supprimée avec succès',
    'message_redeem_successfully' => 'Vous avez échangé avec succès',
    'message_already_redeem' => 'Vous avez déjà utilisé cette réduction ! Passez une commande et profitez de cette réduction !',
    'message_not_enough_point' => ':point crédits nécessaires',
    'message_unable_re_redeemable' => 'Vous avez déjà échangé une autre réduction ! Utilisez-la avant d\'en échanger une nouvelle !',
    'message_redeem_retrieved_successfully' => 'L\'échange a été récupéré avec succès',
    'not_found' => 'Carte de fidélité non trouvée',

    'text_credits' => ':number CRÉDITS',

    'button_redeem' => 'Échanger',
    'already_redeem' => 'Déjà échangé',
    'not_use_loyalty_card' => ':restaurant_name n\'utilise pas de carte de fidélité',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
