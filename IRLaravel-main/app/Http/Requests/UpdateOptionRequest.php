<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;
use App\Traits\APIResponse;

class UpdateOptionRequest extends APIRequest
{
    use APIResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required',
            'min'          => 'required',
            'max'          => 'required|integer|min:' . $this->min,
            'items.*.name' => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name'         => trans('option.validation.name'),
            'min'          => trans('option.validation.min'),
            'max'          => trans('option.validation.max'),
            'items.*.name' => trans('option.validation.items.name'),
        ];
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator|void
     */
    public function prepareForValidation()
    {
        $inputSwitch = array();

        if (!$this->has('is_ingredient_deletion')) {
            $inputSwitch['is_ingredient_deletion'] = 0;
        }

        $inputSwitch['min'] = $this->min === "0" ? 0 : ltrim($this->min, '0');
        $inputSwitch['max'] = $this->max === "0" ? 0 : ltrim($this->max, '0');

        $this->merge($inputSwitch);
    }
}
