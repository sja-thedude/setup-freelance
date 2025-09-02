<?php

$originalTranslations = [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'Invalid combination! Please try again.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'banned'   => 'Your account has been deactivated by the administrators. Please contact them for more details.',
    'invalid_email_or_password'         => 'Invalid combination! Please try again.',
    'failed_to_create_token'            => 'Creating token failed',
    'login_success'                     => 'You have been logged in successfully',
    'success' => 'You have been logged in successfully',
    'message_activated_successfully' => 'Your account has been activated and is ready for use!',
    'message_verified_successfully' => 'Your account has been successfully created.',
    'get_profile_success'               => 'You have retrieved the profile successfully',
    'token_is_invalid'                  => 'Token is invalid',
    'token_is_expired'                  => 'The link you used is no longer valid. Please try again.',
    'something_is_wrong'                => 'Something is wrong',
    'language_not_supported'            => 'Language not supported',
    'invalid_current_password'          => 'Current password is invalid',
    'changed_password_successfully'     => 'You have changed the password successfully',
    'forgot_password_send_link_success' => 'We have e-mailed your password reset link!',
    'forgot_password_send_link_failed'  => 'We can\'t send email. Please try again!',
    'changed_status_successfully'       => 'You have changed the status successfully',
    'invalid_permission'                => 'Your account doesn\'t have this permission',

    'message_email_address_not_recognised' => 'This email address was not recognized.',
    'message_register_successfully' => 'Check your inbox or spam mailbox and follow the link to activate your account.',
    'login' => 'Login',
    'forgot_password' => 'Forgot password?',
    'email' => 'Email address',
    'password' => 'Password',
    'remember' => 'Remember me',
    'back_to_login' => 'Back to login',
    'restore_password' => 'Restore password',
    'forgot_password_subject' => 'Reset your It’s Ready Manager password',

    /* Descriptions */
    'login_description' => '<p class="font-18">Log in with your <a class="text-underline" href="https://itsready.be/" target="_blank">It’s Ready</a> account and start your order.</p>',
    'login_description_group' => '<p class="font-18" style="max-width:620px">Log in with your <a class="text-underline" href="https://itsready.be/" target="_blank">It’s Ready</a> account and place a group order. Is your <b>company</b>, <b>group</b> or <b>class</b> not yet registered with us? <a href=":url">Contact us</a>.</p>',

    /* Buttons */
    'button_login' => 'Login',
    'button_register' => 'Register',
    'fill_in_all_fields'                => 'Please fill in all fields.',

    'validation' => [
        'email_required' => 'Email is invalid',
        'password_required' => 'Password is required',
        'current_password_required' => 'Current password is required',
        'new_password_required' => 'New password is required',
        'password_confirmation_required' => 'Password confirmation is required',
    ],

    'login_modal' => [
        'title' => 'Craving something delicious? Just a moment...',
        'description' => 'Log in with your <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It’s Ready</a> account or create a new account.',
        'button_register' => 'No account yet? Register now',
    ],

    'register_modal' => [
        'title' => 'Craving something delicious? Just a moment...',
        'description' => 'Fill in your details to create an <a href="https://itsready.be/" target="_blank" style="text-decoration: underline; font-size: 18px;">It’s Ready</a> account.',
        'button_register' => 'Register',
        'button_back' => '< Back',
        'confirmation_title' => 'Email sent',
        'confirmation_description' => 'Check your inbox or spam mailbox and follow the link to activate your account.',
    ],

    'forgot_password_modal' => [
        'title' => 'Craving something delicious? Just a moment...',
        'description' => 'Enter your email address to reset your password.',
        'button_back' => '< Back',
        'button_back_naar' => '< Back to login',
        'confirmation_title' => 'Email sent',
        'password_changed' => 'Password changed',
        'button_back_naar_itsready' => 'Back to itsready.be',
        'confirmation_description' => 'Check your inbox or spam mailbox and reset your password.',
    ],
    'ready' => 'Ready!',
    'of' => 'Or',
    'login_with' => 'Login with',
    'register_with' => 'Register with',
    'email_short' => 'Email',
    'login_v2' => 'Login',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
