<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

class AppBaseController extends Controller {

    /**
     * AppBaseController constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Send a success response
     *
     * @param mixed $result
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message) {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    /**
     * Send a error response
     *
     * @param string $error
     * @param int $code HTTP response code
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $code = 404, $data = []) {
        return Response::json(ResponseUtil::makeError($error, $data), $code);
    }

}
