<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;
use App\Models\Workspace;
use App\Traits\APIResponse;

class UpdateWorkspaceManagerRequest extends APIRequest
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
        $id = $this->route('id');
        $validationPhone = config('validation.phone');

        $rules = array_merge(Workspace::$rules, [
            'name' => 'required|max:255',
            'gsm' => array_merge(['required'], $validationPhone),
            'email' => 'required|email|max:255|unique:workspaces,email,' . $id . ',id,deleted_at,NULL',
            'address' => 'required',
            'btw_nr' => 'required',
            'language' => 'required',
            'types' => 'required|array|between:1,6',
            'uploadAvatar' => 'nullable|mimes:jpg,png,jpeg',
            'galleries.*' => 'nullable|mimes:jpg,png,jpeg',
        ]);

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'uploadAvatar.mimes' => trans('workspace.upload_image_mimes'),
            'galleries.*.mimes' => trans('workspace.upload_image_mimes'),
            'types.between' => trans('workspace.select_max_types')
        ];
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
