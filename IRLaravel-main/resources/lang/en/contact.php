<?php

$originalTranslations = [
    'message_retrieved_successfully' => 'Contact has been retrieved successfully.',
    'message_retrieved_list_successfully' => 'Contacts have been retrieved successfully.',
    'message_created_successfully' => 'Contact has been created successfully.',
    'message_updated_successfully' => 'Contact has been updated successfully.',
    'message_saved_successfully' => 'Contact has been saved successfully.',
    'message_deleted_successfully' => 'Contact has been deleted successfully.',
    'not_found' => 'Contact not found',
    'back' => 'Back',
    'fill_info' => 'Please fill in the information below if you wish to order as a <b>company</b>, <b>group</b>, or <b>class</b>.<br> We will contact you shortly!',
    'name_and_surname' => 'Name and Surname',
    'phone' => 'Phone/Mobile',
    'address' => 'City',
    'send' => 'SEND',
    'company' => 'Company',
    'email' => 'Email Address',
    'message' => 'Message',
    'message_sent_successfully' => 'Message has been sent successfully. We will contact you as soon as possible.',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);