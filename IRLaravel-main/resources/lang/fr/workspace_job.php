<?php

$originalTranslations = [
    'message_sent_successfully' => 'Le message a été envoyé avec succès. Nous vous contacterons dès que possible.',
    'message_created_successfully' => 'L\'application Workspace a été créée avec succès.',
    'message_updated_successfully' => 'L\'application Workspace a été mise à jour avec succès.',
    'message_saved_successfully' => 'L\'application Workspace a été enregistrée avec succès.',
    'message_deleted_successfully' => 'L\'application Workspace a été supprimée avec succès.',
    'not_found' => 'Application Workspace non trouvée',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);