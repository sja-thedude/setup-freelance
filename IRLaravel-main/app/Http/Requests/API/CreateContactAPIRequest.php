<?php

namespace App\Http\Requests\API;

use App\Models\Contact;
use InfyOm\Generator\Request\APIRequest;

class CreateContactAPIRequest extends APIRequest
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
        return Contact::$rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $validation = [];

        // Fill name field from first_name & last_name if it is null
        if (!$this->has('name')) {
            $validation['name'] = trim(
                $this->get('first_name', '')
                . ' '
                . $this->get('last_name', ''));
        }

        // Merge with custom data
        $this->merge($validation);
    }

}
