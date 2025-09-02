<?php

namespace App\Services\Connector;

use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use App\Models\SettingConnector;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class HendrickxKassaConnector
 * @package App\Services\Connector
 */
class HendrickxKassaConnector {

    /**
     * @var SettingConnector
     */
    protected $settingConnector;

    /**
     * @var bool
     */
    protected $verboseLevel = 0;

    /**
     * @param SettingConnector $settingConnector
     * @param null|int $verboseLevel
     */
    public function __construct(
        SettingConnector $settingConnector,
        $deliveryMethod = null
    ) {
        $this->settingConnector = $settingConnector;
        $this->deliveryMethod = $deliveryMethod;

        $this->verboseLevel = !empty($verboseLevel) && is_numeric($verboseLevel)
            ? (int) $verboseLevel
            : (int) config('connectors.hendrickx_kassas.verbose_level');

        // Initialise client
        $this->initClient();
    }

    /**
     * Create client
     * @return Client
     */
    protected function initClient() {
        if(isset($this->client) && is_a($this->client, 'GuzzleHttp\Client')) {
            return $this->client;
        }

        $baseUri = $this->settingConnector->getEndpointBasedOnDeliveryMethod();

        $this->client = new Client([
            // Kassa will not have a certificate. We aren't able to valid them if it isn't there..
            'verify' => false,

            // Base URI is used with relative requests
            'base_uri' => $baseUri,

            // Timeout if a server does not return a response in 45.0 seconds
            'timeout'  => 45.0,

            // Timeout if the client fails to connect to the server in 5.0 seconds
            'connect_timeout' => 5.0
        ]);

        return $this->client;
    }

    /**
     * Get all products
     *
     * A /GetAllProducts request will return a list with all products available in the POS.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllProducts() {
        try {
            $dateTime = $this->getDateTime();
            $signature = $this->generateSignature($dateTime);

            $uri = $this->makeUri('/GetAllProducts');
            $this->logRequest($dateTime, $uri, '', '', $signature);

            $response = $this->client->request('GET', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ]
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Get free table number
     *
     * When table numbers are not linked to physical tables. A free table number can be requested. Useful
     * when every order needs his own table number. for example for takeaway/deliver orders.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFreeTableNumber() {
        try {
            $dateTime = $this->getDateTime();
            $signature = $this->generateSignature($dateTime);

            $uri = $this->makeUri('/GetFreeTableNumber');
            $this->logRequest($dateTime,  $uri,'', '', $signature);

            $response = $this->client->request('GET', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ]
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Get payment types
     *
     * With request /GetPaymentTypes a list of all available payment types is returned.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPaymentTypes() {
        try {
            $dateTime = $this->getDateTime();
            $signature = $this->generateSignature($dateTime);

            $uri = $this->makeUri('/GetPaymentTypes');
            $this->logRequest($dateTime, $uri, '', '', $signature);

            $response = $this->client->request('GET', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ]
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Get payment types
     *
     * In the POS products can be setup as countdownPLUs. This is a simple stockcounter to inform POS
     * operators if products are (nearly) out of stock. This helps to prevent ordering out of stock products.
     * The current available stock count can be called and used to block products to prevent that a /
     * CreateOrder fails because products are currently out of stock.
     * By using this call, products that are (nearly) out of stock can be taken off the visible menu. Implementing
     * this call gives a POS operator the opportunity to block products just by setting the stock of a product to
     * zero. and don't need to access your application and take it off the menu.
     * With request /GetCountdownPluState a list of all products that are on or under the threshold state
     * will be returned. To select a threshold use parameter ?threshold=
     * When no threshold is available in the request, all products that are active as countdownPLU in the POS
     * are returned in the list.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCountdownPluState() {
        try {
            $dateTime = $this->getDateTime();
            $signature = $this->generateSignature($dateTime);

            $uri = $this->makeUri('/GetCountdownPluState');
            $this->logRequest($dateTime, $uri, '', '', $signature);

            $response = $this->client->request('GET', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ]
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Table numbers
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTableNumbers() {
        try {
            $dateTime = $this->getDateTime();
            $signature = $this->generateSignature($dateTime);

            $uri = $this->makeUri('/TableNumbers');
            $this->logRequest($dateTime, $uri, '', '', $signature);

            $response = $this->client->request('GET', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ]
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Get product selection
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProductSelection() {
        try {
            $dateTime = $this->getDateTime();
            $signature = $this->generateSignature($dateTime);

            $uri = $this->makeUri('/GetProductSelection');
            $this->logRequest($dateTime, $uri, '', '', $signature);

            $response = $this->client->request('GET', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ]
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Test API credentials
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doTest() {
        try {
            $dateTime = $this->getDateTime();
            $signature = $this->generateSignature($dateTime);

            $uri = $this->makeUri('/test');
            $this->logRequest($dateTime, $uri, '', '', $signature);

            $response = $this->client->request('GET', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ]
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Create order
     *
     * With request /CreateOrder an order is pushed to the POS, if the order is processed by the POS a
    " IsSuccessStatusCode": "true" is returned otherwise "IsSuccessStatusCode": "false".
     * When the IsSuccessStatusCode is not true than the order is not created in the POS, when one or more
     * products are not posted the full order is canceled.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrder($data) {
        try {
            $dateTime = $this->getDateTime();

            $bodyData = $this->generateBody($data);
            $body = $this->encrypt($bodyData);
            $signature = $this->generateSignature($dateTime, $body);

            $uri = $this->makeUri('/CreateOrder');
            $this->logRequest($dateTime, $uri, $bodyData, $body, $signature);

            $response = $this->client->request('POST', $uri, [
                RequestOptions::HEADERS => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ],
                RequestOptions::BODY => $body
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * Pay bill
     *
     * With request /PayBill a bill can be paid in the POS. After the payment is accepted by the POS, the bill
     * is no longer available for requests.
     * Only one payment type can be used to finalize the bill. The amount that is in the request must match the
     * bills total amount.
     * To know the total amount, use request /GetBill [BillTotal.Payment].
     * If the bill balance is 0 and the bill is still available in the POS a /PayBill with amount 0 will finalize the bill
     * in the POS.
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function payBill($data) {
        try {
            $dateTime = $this->getDateTime();

            $bodyData = $this->generateBody($data);
            $body = $this->encrypt($bodyData);
            $signature = $this->generateSignature($dateTime, $body);

            $uri = $this->makeUri('/PayBill');
            $this->logRequest($dateTime, $uri, $bodyData, $body, $signature);

            $response = $this->client->request('POST', $uri, [
                'headers' => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ],
                'body' => $body
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * PostDeposit
     *
     * With request /PostDeposit a deposit or prepaid amount is added to the bill..
     * This call is very similar to the /PayBill request. difference is:
     * - After a deposit the bill will still be available for requests
     * - Deposit amount does not need to match the bill total.
     * Only one payment type can be used to finalize the deposit.
     * Deposits can be send any moment until a /PayBill is send or done on the POS
     *
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postDeposit($data) {
        try {
            $dateTime = $this->getDateTime();

            $bodyData = $this->generateBody($data);
            $body = $this->encrypt($bodyData);
            $signature = $this->generateSignature($dateTime, $body);

            $uri = $this->makeUri('/PostDeposit');
            $this->logRequest($dateTime, $uri, $bodyData, $body, $signature);

            $response = $this->client->request('POST', $uri, [
                'headers' => [
                    'Date' => $dateTime,
                    'Signature' => $signature
                ],
                'body' => $body
            ]);

            $bodyContents =  $response->getBody()->getContents();
            $this->logResponse($response->getStatusCode(), $bodyContents);

            return $this->processResponse($bodyContents);
        }
        catch(\Exception $e) {
            $this->logException($e);
            return null;
        }
    }

    /**
     * @param $uri
     * @param $params
     * @return mixed|string
     */
    protected function makeUri($uri, $params = null) {
        if(config('connectors.hendrickx_kassas.encryption')) {
            $uri = '/SEC1' . $uri;
        }

        // @todo later implement params to be replaced in the URL. Currently not needed.

        return $uri;
    }

    /**
     * @return false|string
     */
    protected function getDateTime() {
        return date('Y-m-d\TH:i:s');
    }

    /**
     * @param $dateTime
     * @param $data
     * @return string
     */
    protected function generateBody($data) {
        return json_encode($data);
    }

    /**
     * @param $data
     * @return string
     */
    protected function encrypt($data) {
        if(!config('connectors.hendrickx_kassas.encryption')) {
            return $data;
        }

        $encryptionToken = $this->settingConnector->getTokenBasedOnDeliveryMethod();
        $initVector = config('connectors.hendrickx_kassas.init_vector');

        if (strlen($data) % 8) {
            $data = str_pad($data, strlen($data) + 8 - strlen($data) % 8, "\0");
        }

        $crypted = openssl_encrypt($data, 'BF-CBC', $encryptionToken, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING | OPENSSL_DONT_ZERO_PAD_KEY, $initVector);

        return base64_encode($crypted);
    }

    /**
     * @param $encryptedData
     * @return string
     */
    protected function decrypt($encryptedData) {
        if(!config('connectors.hendrickx_kassas.encryption')) {
            return $encryptedData;
        }

        // Data isn't encrypted it's still JSON we receive..
        if(in_array(substr($encryptedData, 0, 1), ['[', '{'])) {
            return $encryptedData;
        }

        $encryptionToken = $this->settingConnector->getTokenBasedOnDeliveryMethod();
        $initVector = config('connectors.hendrickx_kassas.init_vector');

        $decoded = base64_decode($encryptedData);
        $decryptedRaw = openssl_decrypt($decoded, 'BF-CBC', $encryptionToken, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING | OPENSSL_DONT_ZERO_PAD_KEY, $initVector);
        $decrypted = trim($decryptedRaw, "\0");

        $this->logDecrypted($decrypted);

        return $decrypted;
    }

    /**
     * @param $data
     * @return string
     */
    protected function generateSignature($dateTime, $data = '') {
        $signatureKey = $this->settingConnector->getKeyBasedOnDeliveryMethod();

        $encodedData = utf8_encode($dateTime . $data);
        $hash = hash_hmac('sha256', $encodedData, utf8_encode($signatureKey), false);
        $bin = hex2bin($hash);

        return base64_encode($bin);
    }

    /**
     * @param $response
     * @return mixed
     */
    protected function processResponse($response) {
        if(config('connectors.hendrickx_kassas.verbose_level') >= 4) {
            $filePath = storage_path('logs/hendrickx_kassas--'.date('Y-m-d').'.log');
            file_put_contents($filePath, '['.date('Y-m-d H:i:s').'] ENCRYPTED: ' . $response . "\n", FILE_APPEND);
        }

        $response = $this->decrypt($response);

        if(
            is_string($response)
            && in_array(substr($response, 0, 1), ['[', '{'])
        ) {
            $data = json_decode($response);

            if(
                json_last_error() !== JSON_ERROR_NONE
                && config('connectors.hendrickx_kassas.verbose_level') >= 2
            ) {
                Log::info('JSON ERROR - ' . json_last_error() . ': ' . json_last_error_msg());

                if(config('connectors.hendrickx_kassas.verbose_level') >= 4) {
                    $filePath = storage_path('logs/hendrickx_kassas--'.date('Y-m-d').'.log');
                    file_put_contents($filePath, '['.date('Y-m-d H:i:s').'] JSON: ' . $response . "\n", FILE_APPEND);
                }
            }

            return $data;
        }

        return $response;
    }

    /**
     * @param $dateTime
     * @param $uri
     * @param $body
     * @param $signature
     * @return void
     */
    protected function logRequest($dateTime, $uri, $bodyData, $body, $signature) {
        if(config('connectors.hendrickx_kassas.verbose_level') >= 3) {
            Log::info('--');
            Log::info('REQUEST:');
            Log::info('Uri: ' . $uri);
            Log::info('BodyData: ' . $bodyData);
            Log::info('Body: ' . $body);
            Log::info('Date: ' . $dateTime);
            Log::info('Signature: ' . $signature);
        }
    }

    /**
     * @param $decrypted
     * @return void
     */
    protected function logDecrypted($decrypted) {
        if(config('connectors.hendrickx_kassas.verbose_level') >= 3) {
            Log::info('Decrypted: ' . $decrypted);
        }
    }

    /**
     * @param $statusCode
     * @param $body
     * @return void
     */
    protected function logResponse($statusCode, $body) {
        if(config('connectors.hendrickx_kassas.verbose_level') >= 3) {
            Log::info('RESPONSE:');
            Log::info('StatusCode: ' . $statusCode);
            Log::info('Body: ' . $body);
        }
    }

    /**
     * @param $e
     * @return void
     */
    protected function logException($e) {
        Log::error($e->getFile() . ':' . $e->getLine() . ' | ' . $e->getCode() . ': ' . $e->getMessage());
    }
}