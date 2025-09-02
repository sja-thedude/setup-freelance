<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Vat;
use App\Traits\APIResponse;

class CreateVatRequest extends Request
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
        $rules = array_merge(Vat::$rules, [
            'country_id' => 'required',
            'vat.*.take_out' => 'required',
            'vat.*.delivery' => 'required',
            'vat.*.in_house' => 'required',
        ]);

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'vat.*.take_out.required' => trans('vat.validation.take_out_required'),
            'vat.*.delivery.required' => trans('vat.validation.delivery_required'),
            'vat.*.in_house.required' => trans('vat.validation.in_house_required'),
        ];
    }
    
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
