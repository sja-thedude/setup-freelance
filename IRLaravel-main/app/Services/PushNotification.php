<?php

namespace App\Services;

use App\Facades\Helper;
use App\Models\Workspace;
use Carbon\Carbon;
use phpFCMv1\Client as FcmClient;
use phpFCMv1\Data as FcmData;
use phpFCMv1\Notification as FcmNotification;
use phpFCMv1\Recipient as FcmRecipient;

/**
 * Class PushNotification
 * @package App\Services
 */
class PushNotification
{
    /**
     * @const int TIME_TO_LIVE
     * Unit: in second
     * 180 = 60 * 30
     */
    public $timeToLive = 1800;

    /**
     * @var string
     */
    private $logDir;

    /**
     * @var boolean
     */
    private $daily;

    /**
     * PushNotification constructor.
     */
    public function __construct(string $logDir = null)
    {
        // Log dir
        if (empty($logDir)) {
            $logDir = storage_path('logs/push_notifications');
        }

        $this->logDir = $logDir;
        $this->daily = config('fcm.log_daily');
    }

    /**
     * Sending a Downstream Message to Multiple Devices
     *
     * @link https://packagist.org/packages/lkaybob/php-fcm-v1
     * @param array $tokens Device tokens
     * @param string $title
     * @param string $message
     * @param array $data
     * @param Workspace|null $workspace
     * @return DownstreamResponse
     */
    public function pushNotification(array $tokens, string $title, string $message, array $data = [], Workspace $workspace = null)
    {
        if (empty($tokens)) {
            // No tokens to send
            return new DownstreamResponse([]);
        }

        $results = [];
        // Default FCM private key
        $firebasePrivateKey = config('fcm.http.private_file');

        /*if (!empty($workspace->firebase_project)) {
            // Custom FCM private key by workspace
            $configFcm = Helper::changeFirebaseProject($workspace->firebase_project);
            $firebasePrivateKey = array_get($configFcm, 'private_file');
        }*/

        // Client instance should be created with path to service account key file
        $client = new FcmClient(storage_path("app/google-service-accounts/{$firebasePrivateKey}"));

        foreach ($tokens as $token) {
            // 1. Create Necessary class instances, Client, Recipient, Notification/Data
            $recipient = new FcmRecipient();
            // Either Notification or Data (or both) instance should be created
            $notification = new FcmNotification();

            // 2. Setup each instances with necessary information
            // Recipient could accept individual device token,
            // the name of topic, and conditional statement
            $recipient->setSingleRecipient($token);
            // Setup Notification title and body
            $notification->setNotification($title, $message);

            // Setup Data payload
            $payloadData = new FcmData();
            $payloadData->setPayload([
                'data' => $this->processData($data)
            ]);

            // Build FCM request payload
            $client->build($recipient, $notification, $payloadData);

            // 3. Fire in the FCM Server!
            $result = $client->fire();
            // You can check the result
            // If successful, true will be returned
            // If not, error message will be returned

            $results[$token] = $result;
        }

        $downstreamResponse = new DownstreamResponse($results);
        // Process push notification downstream response
        $this->processDownstreamResponse($downstreamResponse);

        // Log
        $this->log($results);

        return $downstreamResponse;
    }

    /**
     * Pre-process data before sending
     * The type of data is map (key: string, value: string)
     * Input only. Arbitrary key/value payload, which must be UTF-8 encoded.
     * The key should not be a reserved word ("from", "message_type", or any word starting with "google" or "gcm").
     *
     * @link https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#Message.FIELDS.data
     * @param array $data
     * @return array
     */
    public function processData(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (is_string($value) || $value === null) {
                // Skip if value is string or null
                continue;
            }

            if (is_array($value)) {
                $value = json_encode($value);
            } elseif (is_object($value)) {
                $value = json_encode($value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } else {
                $value = (string)$value;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Process push notification downstream response
     */
    public function processDownstreamResponse(DownstreamResponse $downstreamResponse)
    {
        $numberSuccess = $downstreamResponse->numberSuccess();
        $numberFailure = $downstreamResponse->numberFailure();
        $numberModification = $downstreamResponse->numberModification();

        // return Array - you must remove all these tokens in your database
        $tokensToDelete = $downstreamResponse->tokensToDelete();

        if (!empty($tokensToDelete)) {
            // Cleanup tokens to delete
            // TODO: Change "update" to "delete" in stable version
            \DB::table('notification_devices')
                ->whereIn('token', $tokensToDelete)
                ->update([
                    'active' => 0
                ]);
        }

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $tokensToModify = $downstreamResponse->tokensToModify();

        if (!empty($tokensToModify)) {
            foreach ($tokensToModify as $oldToken => $newToken) {
                // Replace old token with new token
                \DB::table('notification_devices')
                    ->whereIn('token', [$oldToken])
                    ->update([
                        'token' => $newToken
                    ]);
            }
        }

        // return Array - you should try to resend the message to the tokens in the array
        $tokensToRetry = $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
        $tokensWithError = $downstreamResponse->tokensWithError();

        $this->log([
            '$numberSuccess' => $numberSuccess,
            '$numberFailure' => $numberFailure,
            '$numberModification' => $numberModification,
            '$tokensToDelete' => $tokensToDelete,
            '$tokensToModify' => $tokensToModify,
            '$tokensToRetry' => $tokensToRetry,
            '$tokensWithError' => $tokensWithError,
        ]);
    }

    /**
     * @param array|string $output
     * @return false|int
     */
    private function log($output)
    {
        $logDir = $this->logDir;
        $datetime = Carbon::now();
        $datetimeString = $datetime->toDateTimeString();
        $date = $datetime->format('Ymd');
        $time = $datetime->format('His');
        $logFile = $date . '_' . $time . '.log';

        // Directory by today
        if ($this->daily) {
            $logDir .= '/' . $date;
        }

        // Create new dir if not exist
        \File::isDirectory($logDir) or \File::makeDirectory($logDir, 0777, true, true);

        // Full file path
        $fullFile = $logDir . '/' . $logFile;

        // To string if is array
        if (is_array($output)) {
            $output = var_export($output, true);
        }

        $result = file_put_contents($fullFile,
            '---------- ' . $datetimeString . ' ----------' . PHP_EOL
            . $output . PHP_EOL
            . '--------------------------------------------------------' . PHP_EOL,
            FILE_APPEND);

        return $result;
    }
}

/**
 * Class DownstreamResponse
 * @package App\Services
 *
 * Customize like bellow package class
 * @link https://github.com/brozot/Laravel-FCM/blob/master/src/Response/DownstreamResponse.php
 */
class DownstreamResponse
{
    /**
     * @var array $results
     */
    private $results;

    /**
     * DownstreamResponse constructor.
     * @param array $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * @return int
     */
    public function numberSuccess()
    {
        $count = 0;

        foreach ($this->results as $result) {
            if ($result === true) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return int
     */
    public function numberFailure()
    {
        $count = 0;

        foreach ($this->results as $result) {
            if ($result !== true) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return int
     */
    public function numberModification()
    {
        return 0;
    }

    /**
     * @return array
     */
    public function tokensToDelete()
    {
        $arr = [];

        foreach ($this->results as $key => $result) {
            if ($result !== true) {
                $arr[] = $key;
            }
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function tokensToModify()
    {
        return [];
    }

    /**
     * @return array
     */
    public function tokensToRetry()
    {
        return [];
    }

    /**
     * @return array
     */
    public function tokensWithError()
    {
        $arr = [];

        foreach ($this->results as $key => $result) {
            if ($result !== true) {
                $arr[$key] = $result;
            }
        }

        return $arr;
    }
}