<?php

namespace App\Services;

use JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;

/**
 * Class Socket
 * @package App\Services
 */
class Socket {

    /**
     * @param $event
     * @param array $data
     * @param null $token
     * @return bool
     */
    public function emit($event, $data = array(), $token = null) {
        if(empty($event)) {
            return false;
        }

        if(empty($token)) {
            $token = $this->signToken();
        }
        
        $client = new Client(new Version2X(config('socket.host'), array(
            'version' => config('socket.version'),
            'headers' => array(
                'Authorization: Bearer ' . $token
            ),
            'context' => [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]
        )));

        $client->initialize();        
        $client->emit($event, $data);
        $client->close();

        return true;
    }

    public function signToken() {
        $customClaims = array('sub' => 'generate');
        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload);

        return $token;
    }
}
