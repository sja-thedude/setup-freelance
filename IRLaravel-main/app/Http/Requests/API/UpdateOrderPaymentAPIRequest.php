<?php

namespace App\Http\Requests\API;

/**
 * Class UpdateOrderPaymentAPIRequest
 * @package App\Http\Requests\API
 */
class UpdateOrderPaymentAPIRequest extends MyAPIRequest
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
            'payment_method' => 'required|integer',
            'payment_status' => 'required|integer',
        ];
    }

}
