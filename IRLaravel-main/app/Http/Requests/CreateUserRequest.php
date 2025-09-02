<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;
use App\Models\User;
use App\Traits\APIResponse;

class CreateUserRequest extends APIRequest
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

        $rules = array_merge(User::$rules, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'gsm' => array_merge(['required'], $validationPhone),
        ]);

        return $rules;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
