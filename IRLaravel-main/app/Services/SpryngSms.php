<?php

namespace App\Services;

use App\Models\Sms;
use Carbon\Carbon;
use GuzzleHttp\RequestOptions;
use GuzzleHttp;

class SpryngSms {

    /**
     * Endpoint of Spryng sms
     * @var null|string
     */
    protected $baseEndpoint = null;

    /**
     * AccessToken to communicate with Spryng
     * @var string|null
     */
    protected $accessToken = null;

    /**
     * Route
     * @var null|string
     */
    protected $route = null;


    public function __construct() {
        $this->baseEndpoint = config('sms.base_endpoint');
        $this->accessToken = config('sms.api_key');
        $this->route = config('sms.route');
    }

    /**
     * @param $user
     * @param $accessToken
     * @return mixed
     */
    public function updateSms($sms, $status) {
        $sms->status = $status;
        $sms->sent_at = Carbon::now();

        return $sms->save();
    }

    /**
     * @param $division
     * @param $data
     * @return mixed|string
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public function createSms($sms, $phoneNbs) {
        $data = [
            "body" => $sms->message,
            "encoding" => "auto",
            "originator" => "IT",
            "recipients" => $phoneNbs,
            "route" => $this->route,
            "scheduled_at" => Carbon::now()->toIso8601String()
        ];

        try {
            $client = new GuzzleHttp\Client();
            $response = $client->request('POST', $this->baseEndpoint . '/messages', [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                RequestOptions::JSON => $data
            ]);

            $this->updateSms($sms, Sms::STATUS_SENT);
        }
        catch (GuzzleHttp\Exception\ClientException $e) {
            $this->updateSms($sms, Sms::STATUS_ERROR);
        }
    }
}