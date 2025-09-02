<?php

namespace App\Http\Requests;

use App\Models\Category;
use InfyOm\Generator\Request\APIRequest;
use App\Traits\APIResponse;

class CreateCategoryRequest extends APIRequest
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
        $rulesNew = [
            'name'              => 'required|max:255',
            'uploadAvatar'      => 'mimes:jpeg,jpg,png',
            'days.*.start_time' => 'date_format:H:i',
        ];

        if ($this->time_no_limit) {
            $rulesNew['days.*.end_time'] = 'date_format:H:i|after:days.*.start_time';
        }

//        $productValidations = 'nullable';
//        if (empty($this->get('category')) && empty($this->get('category_ids'))) {
//            $productValidations = 'required';
//        }
//
//        $rulesNew['category'] = $productValidations;

        return array_merge(Category::$rules, $rulesNew);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'days.*.end_time.after' => trans('validation.after_progess_time'),
            'category.required'           => trans('coupon.valid_product'),
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

    /**
     * @return \Illuminate\Contracts\Validation\Validator|void
     */
    public function prepareForValidation()
    {
        $inputSwitch = array();

        if (!$this->has('group')) {
            $inputSwitch['group'] = 0;
        }

        if (!$this->has('individual')) {
            $inputSwitch['individual'] = 0;
        }

        if (!$this->has('available_delivery')) {
            $inputSwitch['available_delivery'] = 0;
        }

        if (!$this->has('favoriet_friet')) {
            $inputSwitch['favoriet_friet'] = 0;
        }

        if (!$this->has('kokette_kroket')) {
            $inputSwitch['kokette_kroket'] = 0;
        }

        if (!$this->has('extra_werkbon')) {
            $inputSwitch['extra_werkbon'] = 0;
        }

        $this->merge($inputSwitch);

        $languages = config('languages');
        $currentLang = app()->getLocale();
        foreach($languages as $langKey => $langLabel) {
            if($langKey == $currentLang) {
                continue;
            }
            $langScan = $this->request->get($langKey);
            $langScan['name'] = $this->name;
            $this->merge([$langKey => $langScan]);
        }
    }
}
