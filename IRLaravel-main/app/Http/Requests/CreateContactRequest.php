<?php

namespace App\Http\Requests;

use App\Models\Contact;
use App\Traits\APIResponse;
use InfyOm\Generator\Request\APIRequest;

class CreateContactRequest extends APIRequest
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
        $validationPhone = config('validation.phone');

        $rules = [
            'name' => 'required|max:255',
            'phone' => array_merge(['required'], $validationPhone),
            'email' => 'required|email|max:255',
            'content' => 'nullable|max:100',
        ];

        return $rules;
    }

    public function messages()
    {
        $messages = parent::messages();

        return array_merge($messages, [
            'phone.min' => trans('register.validation.gsm.min')
        ]);
    }
}
