<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Class AuthJWT
 * @package App\Http\Middleware
 */
class AuthJWT
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
                throw new \Exception('Token not found');
            }

            $id = $parseToken->getPayload()->get('sub');

            if ($id == config('app.key')) {
                // Token without user
                return $next($request);
            }

            if (empty(JWTAuth::parseToken()->authenticate())) {
                return response()->json(['user_not_found'], 401);
            }
        } catch (TokenExpiredException $e) {
            return Response::json(ResponseUtil::makeError($e->getMessage()), $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return Response::json(ResponseUtil::makeError($e->getMessage()), $e->getStatusCode());
        } catch (JWTException $e) {
            return Response::json(ResponseUtil::makeError($e->getMessage()), $e->getStatusCode());
        }

        // the token is valid and we have found the user via the sub claim
        return $next($request);
    }

}
