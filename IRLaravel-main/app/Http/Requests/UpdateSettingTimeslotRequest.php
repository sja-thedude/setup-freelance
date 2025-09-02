<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\SettingTimeslot;
use App\Traits\APIResponse;

class UpdateSettingTimeslotRequest extends Request
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
        return [
            'order_per_slot' => 'required|min:0',
            'max_price_per_slot' => 'required|min:0',
            'max_price_per_slot' => 'required|min:1',
            'max_time' => ['regex:/^([01]\d|2[0-3]):([0-5]\d)$/'],
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
