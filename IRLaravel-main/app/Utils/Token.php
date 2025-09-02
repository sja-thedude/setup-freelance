<?php

namespace App\Utils;

use JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;

/**
 * Class Token
 * @package App\Services
 */
class Token {
    public function signToken($id = 'generate') {        
        $customClaims = array('sub' => $id);
        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload);

        return $token;
    }
}
