<?php

namespace App\Http\Requests;

class CreatePortalContactRequest extends Request
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
        $validationPhone = config('validation.phone');

        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'phone' => array_merge(['required'], $validationPhone),
            'email' => 'required|email|max:255',
            'message' => 'required',
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
