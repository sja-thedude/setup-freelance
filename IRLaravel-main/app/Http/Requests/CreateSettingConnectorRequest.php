<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\SettingConnector;

class CreateSettingConnectorRequest extends Request
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
        $rules = SettingConnector::$rules;

        $rules['provider'] = 'required';

        return $rules;
    }
}
