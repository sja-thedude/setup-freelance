<?php

namespace App\Http\Requests\API;

class ValidateAvailableTimeslotAPIRequest extends MyAPIRequest
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
            'product_id' => 'required|array',
            'date' => 'required_with:time|date_format:Y-m-d',
            'time' => 'date_format:H:i',
        ];
    }
}
