<?php

namespace App\Http\Requests\API;

/**
 * Class UnsubscribeNotificationDeviceAPIRequest
 * @package App\Http\Requests\API
 */
class UnsubscribeNotificationDeviceAPIRequest extends MyAPIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'integer',
            'device_id' => 'required:max:255',
            'type' => 'required',
        ];
    }
}
