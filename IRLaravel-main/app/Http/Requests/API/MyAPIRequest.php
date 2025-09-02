<?php

namespace App\Http\Requests\API;

use App\Facades\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InfyOm\Generator\Request\APIRequest;
use Illuminate\Http\Request;

/**
 * @override from \InfyOm\Generator\Request\APIRequest
 *
 * Class MyAPIRequest
 * @package App\Http\Requests\API
 */
class MyAPIRequest extends APIRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $result = [
            'success' => false,
            'data' => null,
            'message' => Helper::getFirstErrorMessage($errors),
            'errors' => $errors,
        ];

        throw new HttpResponseException(
            response()->json($result, JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Determine if the request is from a web browser.
     *
     * @param Request $request
     * @return bool
     */
    protected function isFromBrowser(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        // A simple check for common browser strings
        if (strpos($userAgent, 'Mozilla') !== false || strpos($userAgent, 'Chrome') !== false || strpos($userAgent, 'Safari') !== false) {
            //return "This request is likely from a web browser.";
            return true;
        } else {
            //return "This request is likely not from a web browser.";
            return false;
        }
    }
}
