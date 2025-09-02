<?php

namespace App\Http\Requests;

class ChangeAppSettingsRequest extends Request
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
        $rules = [
            'name' => 'max:10',
            'title' => 'max:25',
            'description' => 'max:91',
            'url' => 'required_if:type,1|url',
        ];

        // Special validation
        $key = $this->get('key');
        $default = filter_var($this->get('default'), FILTER_VALIDATE_BOOLEAN);

        // With key is "job"
        if ($key == 'jobs') {
            $rules = array_merge($rules, [
                'title' => 'required|max:100',
                'content' => 'required',
            ]);
        }

        // Condition with non-default records
        if (!$default) {
            $rules = array_merge($rules, [
                'title' => 'required_if:type,1|max:25',
            ]);
        }

        return $rules;
    }
}
