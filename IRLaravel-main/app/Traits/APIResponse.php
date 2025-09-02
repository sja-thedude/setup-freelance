<?php

namespace App\Traits;

use InfyOm\Generator\Utils\ResponseUtil;
use Response;

trait APIResponse {
    public function sendResponse($result, $message) {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404, $data = []) {
        return Response::json(ResponseUtil::makeError($error, $data), $code);
    }

    public function apiFailedValidation($validator, $isFirst = false) {
        $errors = [];

        if(!empty($validator->getMessageBag()->messages())) {
            foreach($validator->getMessageBag()->messages() as $key => $message) {
                $arrayMes = array_flatten($message);

                $errors[$key] = implode(' ', $arrayMes);

                if ($isFirst && count($arrayMes) > 0) {
                    $errors[$key] = $arrayMes[0];
                }
            }
        }

        return $errors;
    }
}
