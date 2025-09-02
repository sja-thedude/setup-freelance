<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\User;
use App\Traits\APIResponse;

class UpdateUserRequest extends Request
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
        // Get data from route request
        $auth = null;
        $routeName = $this->route()->getName();

        if ($routeName == 'admin.users.updateProfile') {
            $auth = auth(config('module.backend'))->user();
        } elseif ($routeName == 'manager.users.updateProfile') {
            $auth = auth(config('module.manager'))->user();
        }

        $user = $this->route('user');

        if (!empty($auth)) {
            $user = $auth;
        }

        $validationPhone = config('validation.phone');

        $rules = array_merge(User::$rules, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'locale' => 'string|size:2',
            'gsm' => array_merge(['required'], $validationPhone),
            'email' => 'required|email|max:255|unique:users,email,' . $user->id . ',id,deleted_at,NULL',
        ]);

        return $rules;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
