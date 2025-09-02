<?php

namespace App\Http\Requests;

/**
 * Class RegisterRequest
 * @package App\Http\Requests
 */
class RegisterRequest extends Request
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
        $validationName = config('validation.name');

        $rules = [
            'first_name' => array_merge(['required'], $validationName),
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|blacklist|max:255|unique:users',
            'password' => 'required|min:6|string|', // confirmed
            'password_confirmation' => 'required|min:6|string|same:password',
            'birthday' => 'nullable|date|before:13 years ago|after:-100 years',
            'address' => 'nullable|string',
            'gsm' => array_merge(['required'], $validationPhone),
        ];

        if (request('platform') === 'web') {
            $rules['checkbox-register'] = 'required';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @overwrite
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
            'email.unique' => trans('register.validation.email.unique'),
            'email.email' => trans('register.validation.email.email'),
            'password.min' => trans('passwords.validation.password.min'),
            'password_confirmation.min' => trans('passwords.validation.password.min'),
            'password_confirmation.same' => trans('passwords.validation.password.same'),
            'birthday.before' => trans('user.validation.birthday_13'),
            'birthday.date' => trans('register.validation.birthday.date'),
            'gsm.min' => trans('register.validation.gsm.invalid'),
            'gsm.max' => trans('register.validation.gsm.invalid'),
            'gsm.regex' => trans('register.validation.gsm.invalid'),
            'first_name.regex' => trans('user.validation.first_name_regex'),
            '*.required' => trans('register.field_required')
        ]);
    }

}
