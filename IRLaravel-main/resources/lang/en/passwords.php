<?php

$originalTranslations = [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Passwords must be at least six characters and match the confirmation.',
    'reset'    => 'Your new password has been set successfully.',
    'reset_description' => 'Enter your new password.',
    'sent'     => 'Check your inbox or spam mailbox and follow the link to reset your password.',
    'token'    => 'The link you used is no longer valid. Please try again.',
    'user'     => "We can't find a user with that e-mail address.",
    'forgot_when_did_reset' => 'Please contact the Admin of the platform to reset your password.',
    'description' => '<p class="font-18">Enter your email address to reset your password.</p>',
    'button_reset_password' => 'RESET PASSWORD',
    'set_new_passwords' => 'Set new password',
    'set_password' => 'Set password',

    /* Placeholders */
    'placeholders' => [
        'password' => 'New password',
        'password_confirmation' => 'Repeat new password',
    ],

    'validation' => [
        'password' => [
            'confirmed' => 'Passwords do not match.',
            'same' => 'Passwords do not match.',
            'min' => 'Please use at least 6 characters.',
        ],
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
