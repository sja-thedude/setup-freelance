<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;
use App\Models\User;
use App\Traits\APIResponse;
use Hash;

class AdminChangePasswordRequest extends APIRequest
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
        $user = auth('admin')->user();
        $rules = [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    // Validate current password
                    if (!Hash::check($value, $user->password)) {
                        $fail(trans('messages.user.invalid_current_password'));
                    }
                },
            ],
            'new_password' => 'required|min:6|different:current_password',
            'password_confirmation' => 'required|min:6|same:new_password'
        ];

        return $rules;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
