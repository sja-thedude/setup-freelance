<?php

namespace App\Utils;

use JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

/**
 * Class Socket
 * @package App\Services
 */
class Jwt {
    public function signToken($id) {
        $customClaims = array('sub' => $id);
        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload);

        return $token;
    }
}
