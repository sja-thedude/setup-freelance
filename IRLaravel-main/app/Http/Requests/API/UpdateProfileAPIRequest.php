<?php

namespace App\Http\Requests\API;

/**
 * Class UpdateProfileAPIRequest
 * @package App\Http\Requests\API
 */
class UpdateProfileAPIRequest extends MyAPIRequest
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
        /** @var \App\Models\User $user */
        $user = \Auth::user();
        $validationPhone = config('validation.phone');
        $validationName = config('validation.name');

        if ($this->required_only_gsm) {
            $rules = [
                'gsm'        => array_merge(['required'], $validationPhone),
                'first_name' => array_merge(['required'], $validationName),
            ];
        } else {
            $rules = [
                'first_name' => array_merge(['required'], $validationName),
                'last_name'  => array_merge(['required'], $validationName),
                'email'      => 'required|string|email|max:255|unique:users,email,' . $user->id . ',id',
                'birthday'   => 'nullable|date|before:13 years ago|after:-100 years',
                'address'    => 'nullable|string',
                'gsm'        => array_merge(['required'], $validationPhone),
            ];
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
            'email.unique'    => trans('register.validation.email.required'),
            'birthday.before' => trans('user.validation.birthday_13'),
            'first_name.regex' => trans('user.validation.first_name_regex'),
        ]);
    }

}
