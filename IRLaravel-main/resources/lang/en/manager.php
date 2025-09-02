<?php

$originalTranslations = [
    'created_successfully' => 'Manager has been created',
    'updated_successfully' => 'Manager has been updated',
    'deleted_successfully' => 'Successfully deleted',
    'deleted_confirm' => 'This account manager has been successfully deleted.',
    'not_found' => 'Manager not found',

    'add_manager' => 'New account manager',
    'edit_manager' => 'Edit account manager',
    'detail_manager' => 'Detail account manager',
    'are_you_sure_delete' => 'Are you sure you want to delete <strong>:name</strong>?',

    'title' => 'Account managers',
    'name' => 'Name',
    'email' => 'E-mail',
    'gsm' => 'Gsm',
    'status' => 'Status',
    'active_date' => 'Active date',
    'actions' => 'Actions',
    'status_0' => 'Active',
    'status_1' => 'Invitation expired',
    'status_2' => 'Invitation sent',
    'send_invitation' => 'Send invitation',
    'choose_another_manager' => 'First, assign customers to another account manager!',
    'delete_account_manager' => 'Delete account manager',
    'account_manager' => 'Account manager',
    'resend_invitation' => 'Resend invitation',
    'reset_invitation_confirm' => 'Are you sure you want to resend the invitation?',
    'send_invitation_subject' => 'Welcome to It’s Ready',
    'sent_invitation' => 'You have sent the invitation successfully.',
    'sent_invitation_success' => 'Successfully sent',
    'first_name' => 'First name',
    'last_name' => 'Last name',
    'edit_profile' => 'Edit profile',
    'change_password' => 'Change password',
    'add_account_manager' => 'Add account manager',
    'submit_new_account_manager' => 'Send invitation',

    'validation' => [
        'name_required' => 'Name is required',
        'email_required' => 'E-mail is invalid'
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);