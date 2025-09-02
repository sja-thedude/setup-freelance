<?php

namespace App\Http\Requests\API;

class ChangePasswordAPIRequest extends MyAPIRequest
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
            'current_password' => 'required|max:255',
            'new_password' => 'required|min:6|max:255|same:password_confirmation',
            'password_confirmation' => 'required|min:6|max:255|same:new_password'
        ];
    }

}
