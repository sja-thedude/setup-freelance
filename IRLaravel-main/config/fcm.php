<?php

if (!function_exists('get_string_between')) {
    /**
     * Get string between 2 strings
     *
     * @link https://stackoverflow.com/a/9826656
     *
     * @param $string
     * @param $start
     * @param $end
     * @return false|string
     */
    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}

$arrProjects = [];

$fcmProjects = array_filter($_ENV, function ($key) use (&$arrProjects) {
    $parsed = get_string_between($key, 'FCM_PROJECT_', '_PRIVATE_FILE');

    // Ignore
    if (trim($parsed) == '') {
        return false;
    }

    // Firebase Project
    $arrProjects[env("FCM_PROJECT_{$parsed}_ID")] = [
        'project_id' => env("FCM_PROJECT_{$parsed}_ID"),
        'private_file' => env("FCM_PROJECT_{$parsed}_PRIVATE_FILE"),
        'name' => env("FCM_PROJECT_{$parsed}_NAME"),
        'server_key' => env("FCM_PROJECT_{$parsed}_SERVER_KEY"),
        'sender_id' => env("FCM_PROJECT_{$parsed}_SENDER_ID")
    ];

    return true;
}, ARRAY_FILTER_USE_KEY);

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => env('FCM_LOG_ENABLED', false),
    'log_daily' => env('FCM_LOG_DAILY', true),

    'http' => [
        'project_id' => env('FCM_PROJECT_ID', 'Your FCM project ID'),
        'private_file' => env('FCM_PRIVATE_FILE', 'service_account.json'),
        'server_key' => env('FCM_SERVER_KEY', 'Your FCM server key'),
        'sender_id' => env('FCM_SENDER_ID', 'Your sender id'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],

    'default_project' => [
        'project_id' => env('FCM_PROJECT_ID', 'Your FCM project ID'),
        'private_file' => env('FCM_PRIVATE_FILE', 'service_account.json'),
        'server_key' => env('FCM_SERVER_KEY', 'Your FCM server key'),
        'sender_id' => env('FCM_SENDER_ID', 'Your sender id'),
    ],

    // All Firebase Projects
    'projects' => $arrProjects,
];
