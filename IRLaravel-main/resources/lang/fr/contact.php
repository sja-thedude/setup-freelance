<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Le contact a été récupéré avec succès.',
    'message_retrieved_list_successfully' => 'Les contacts ont été récupérés avec succès.',
    'message_created_successfully' => 'Le contact a été créé avec succès.',
    'message_updated_successfully' => 'Le contact a été mis à jour avec succès.',
    'message_saved_successfully' => 'Le contact a été enregistré avec succès.',
    'message_deleted_successfully' => 'Le contact a été supprimé avec succès.',
    'not_found' => 'Contact non trouvé',
    'back' => 'Retour',
    'fill_info' => 'Remplissez les informations ci-dessous si vous souhaitez commander en tant qu\'<b>entreprise</b>, <b>groupe</b> ou <b>classe</b>.<br> Nous vous contacterons rapidement !',
    'name_and_surname' => 'Nom et prénom',
    'phone' => 'Téléphone/Mobile',
    'address' => 'Commune',
    'send' => 'ENVOYER',
    'company' => 'Entreprise',
    'email' => 'Adresse e-mail',
    'message' => 'Message',
    'message_sent_successfully' => 'Le message a été envoyé avec succès. Nous vous contacterons dès que possible.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);