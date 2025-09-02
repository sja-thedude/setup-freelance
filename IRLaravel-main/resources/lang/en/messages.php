<?php

$originalTranslations = [
    'not_found'         => 'Not found',
    'success'           => 'Success',
    'fail'              => 'Fail',
    'access_denied'     => 'Access denied',
    /* User domain */
    'user'              => [
        'not_found'                     => 'User not found',
        'created_successfully'          => 'User has been created',
        'updated_successfully'          => 'User has been updated',
        'deleted_successfully'          => 'User has been deleted',
        'change_password_successfully'  => 'You changed the password successfully.',
        'updated_profile_successfully'  => 'You have successfully updated your profile.',
        'your_account_was_banned'       => 'You are not able to log in now because your account was banned. Please contact Web Owner for more information!',
        'invalid_login_type'            => 'Invalid login type',
        'verified_account_successfully' => 'You have successfully verified your account.',
        'invalid_current_password'      => 'Current password is invalid',
        'changed_password_successfully' => 'You have successfully changed your password',
        'changed_email_successfully'    => 'E-mail successfully changed.',
    ],
    'admin'             => [
        'created_successfully'         => 'You have created a new user successfully. The password was sent to his email address.',
        'updated_successfully'         => 'User has updated successfully.',
        'deleted_successfully'         => 'User has been deleted',
        'profile_updated_successfully' => 'Your profile has updated successfully.',
    ],
    /* Role domain */
    'role'              => [
        'not_found'            => 'Role not found',
        'created_successfully' => 'Role has been created',
        'updated_successfully' => 'Role has been updated',
        'deleted_successfully' => 'Role has been deleted',
    ],
    /* Country domain */
    'country'           => [
        'not_found'            => 'Country not found',
        'created_successfully' => 'Country has been created',
        'updated_successfully' => 'Country has been updated',
        'deleted_successfully' => 'Country has been deleted',
    ],
    /* Banner domain */
    'banner'            => [
        'not_found'            => 'Banner not found',
        'created_successfully' => 'Banner has been created',
        'updated_successfully' => 'Banner has been updated',
        'deleted_successfully' => 'Banner has been deleted',
    ],
    /* Contact domain */
    'contact'           => [
        'not_found'            => 'Contact not found',
        'created_successfully' => 'Thanks for sending feedback to us.',
    ],
    /* Post domain */
    'post'              => [
        'not_found'            => 'Post not found',
        'created_successfully' => 'Post has been created',
        'updated_successfully' => 'Post has been updated',
        'deleted_successfully' => 'Post has been deleted',
    ],
    /* Category domain */
    'category'          => [
        'not_found'            => 'Category not found',
        'created_successfully' => 'Category has been created',
        'updated_successfully' => 'Category has been updated',
        'deleted_successfully' => 'Category has been deleted',
    ],
    'lang'              => 'Language has been saved',
    'workspace_offline' => 'This restaurant is not online so the order cannot be created. Please try again later!',
    'upload_successfully' => 'Upload successfully',
    'upload_fail' => 'Upload fail',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);
