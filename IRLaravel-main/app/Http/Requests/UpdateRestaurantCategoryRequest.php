<?php

namespace App\Http\Requests;

use App\Models\RestaurantCategory;
use InfyOm\Generator\Request\APIRequest;
use App\Traits\APIResponse;

class UpdateRestaurantCategoryRequest extends APIRequest
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
        $id = $this->route('type_zaak');
        
         $rules = array_merge(RestaurantCategory::$rules, [
             'name' => 'required|unique:restaurant_categories,name,' . $id
        ]);

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
