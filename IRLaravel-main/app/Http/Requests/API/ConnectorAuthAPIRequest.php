<?php

namespace App\Http\Requests\API;

class ConnectorAuthAPIRequest extends MyAPIRequest
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
            'client_id' => 'required',
            'grant_type' => 'required',
            'refresh_token' => 'required'
        ];
    }
}
