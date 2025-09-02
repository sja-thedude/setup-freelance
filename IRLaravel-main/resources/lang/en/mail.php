<?php

$originalTranslations = [
    'user'          => [
        'subject_create_admin'         => 'Priceless-IT new Admin account created',
        'subject_account_verification' => 'Activate your It\'s Ready account',
        'subject_confirm_change_email' => 'Confirm request to change email address',
        'subject_reset_password'       => 'Reset your It\'s Ready password',
    ],
    'contact'       => [
        'subject_new_contact' => 'New group order request via It\'s Ready',
        'subject_contact_to_admin' => 'Message from the contact form',
    ],
    'job'           => [
        'subject_new_job' => 'Response to your job posting via your app',
    ],
    'reward'        => [
        'subject_physical_gift' => ':title successfully redeemed!',
    ],
    'reminder'      => [
        'subject'      => 'Your order is almost ready!',
        'content1'     => "Email subject",
        'content2'     => "Hello :first_name",
        'content3'     => "Your order <b>:order_id</b> at <b>:restaurant</b> will be ready for <b>pickup</b> at <b>:time</b>",
        'content4'     => "Thank you for your order and enjoy your meal!",
        'content5'     => "Order overview",
        'content12'    => "Your order <b>:order_id</b> at <b>:restaurant</b> will be <b>delivered</b> at :time.",
        'content_note' => 'Note: this is a test order',
    ],
    'order_success' => [
        'subject'      => 'Thank you for your order :code!',
        'content1'     => "Email subject",
        'content2'     => "[0,1]Hello :first_name|{2}Dear|{3}Hello :first_name|[4,*]Dear",
        'content3'     => "Thank you for your order at <b>:restaurant</b>. Here is your order confirmation for order <b>:order_id</b>. You can pick up your order <b>:type</b> on <b>:date</b> at <b>:time</b>.",
        'content4'     => "Thank you for your order and enjoy your meal!",
        'content5'     => "Order overview",
        'content12'    => "Thank you for your order! Here is your order confirmation for order <b>:order_id</b>. Your order at <b>:restaurant</b> will be <b>delivered</b> on <b>:date</b> at <b>:time</b>.",
        'content_note' => 'Note: this is a test order',
        'short_description' => "Thank you for your order at <b>:restaurant</b>. Here is your order confirmation for order <b>:order_id</b>.",
    ],
    'print_job_monitor' => [
        'subject' => 'Printer issue detected!'
    ],
    'hendrickx_kassas_failed' => [
        'subject_fatal' => 'Fatal error: Problem with sending the order to Hendrickx Kassas!',
        'subject_notice' => 'Notice: Problem with sending the order to Hendrickx Kassas!',
    ],
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);