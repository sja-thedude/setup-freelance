<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;
use App\Models\SettingPreference;
use App\Traits\APIResponse;

class CreateSettingPreferenceRequest extends APIRequest
{
    use APIResponse;
    
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
        $rules = array_merge(SettingPreference::$rules, [
            'takeout_min_time' => 'nullable|numeric',
            'delivery_min_time' => 'nullable|numeric',
            'takeout_day_order' => 'nullable|numeric|max:14',
            'delivery_day_order' => 'nullable|numeric|max:14',
            'mins_before_notify' => 'nullable|numeric',
        ]);

        return $rules;
    }
    
    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
