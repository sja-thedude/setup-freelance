<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

/**
 * Class Connector
 * @package App\Http\Middleware
 */
class Connector
{

    /**
     * Handle an incoming request.
     *
     * Retrieving the Authenticated user from a token
     * @link https://github.com/tymondesigns/jwt-auth/wiki/Authentication
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        try {
            $parseToken = JWTAuth::parseToken();

            if (empty($parseToken)) {
                return response()->json(['Token not found'], 404);
            }

            $token = $parseToken->getToken();
            $settingConnector = \App\Models\SettingConnector::where('token', $token)
                ->where('provider', \App\Models\SettingConnector::PROVIDER_CUSTOM)
                ->first();

            if(empty($settingConnector)) {
                return response()->json(['Token invalid'], 401);
            }

            $key = InMemory::plainText($settingConnector->key);
            $config = Configuration::forSymmetricSigner(new Sha256(), $key);
            assert($config instanceof Configuration);
            $tokenParse = $config->parser()->parse($settingConnector->token);
            $tokenClaims = $tokenParse->claims();

            if($tokenClaims->has('exp') && $tokenClaims->get('exp')->getTimeStamp() < strtotime(now())) {
                return response()->json(['Token expired'], 401);
            }

            if(!$tokenClaims->has('secret')) {
                return response()->json(['Token invalid'], 401);
            }

            $request->request->add([
                'workspaceId' => $settingConnector->workspace_id
            ]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 401);
        }

        return response()->json(['Token invalid'], 401);
    }
}
