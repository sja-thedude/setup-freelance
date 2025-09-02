<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;
use App\Http\Requests\Request;
use App\Models\Workspace;
use App\Traits\APIResponse;

class UpdateWorkspaceRequest extends APIRequest
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
        $id = $this->route('restaurant');
        $workspace = Workspace::find($id);
        $email = $this->email;
        $validationPhone = config('validation.phone');

        $rules = array_merge(Workspace::$rules, [
            'name' => 'required|max:255',
            'account_manager_id' => 'required',
            'gsm' => array_merge(['required'], $validationPhone),
            'manager_name' => 'required',
            'surname' => 'required',
            'address' => 'required',
            'btw_nr' => 'required',
            'language' => 'required',
            'email' => 'required|email|max:255|unique:workspaces,email,' . $id . ',id,deleted_at,NULL',
            'country_id' => 'required',
            'types' => 'required',
            'uploadAvatar' => 'nullable|mimes:jpg,png,jpeg',
            'slug' => 'required|max:255|unique:workspaces,slug,' . $id . ',id,deleted_at,NULL',
            'email_to' => 'required',
        ]);

        if ($email && $email != $workspace->email) {
            $emailRule = explode('|', $rules['email']);
            $emailRule = array_merge($emailRule, [
                'unique:users'
            ]);
            $rules['email'] = $emailRule;
        }

        return $rules;
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator|void
     */
    public function prepareForValidation()
    {
        if (!$this->has('slug')) {
            $this->merge([
                'slug' => str_slug($this->slug, '-')
            ]);
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'slug' => trans('workspace.workspace_subdomain'),
        ];
    }
}
