<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;
use App\Models\Notification;
use App\Traits\APIResponse;

class CreateNotificationRequest extends APIRequest
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
        $rules = array_merge(Notification::$rules, [
            'title' => 'nullable|max:255',
            'description' => 'required',
        ]);
        
        if ($this->has('send_now') && $this->send_now == 0) {
            $rules['send_datetime'] = 'required';
        }
        
        if ($this->has('is_send_everyone') && $this->is_send_everyone == 0) {
            $rules['location'] = 'required';
            $rules['location_radius'] = 'required';
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
}
