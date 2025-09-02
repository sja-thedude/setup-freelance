<?php

namespace App\Http\Requests;

use App\Rules\CheckProductAvailabilityRule;
use InfyOm\Generator\Request\APIRequest;
use App\Traits\APIResponse;

class CreateProductRequest extends APIRequest
{
    use APIResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $checkProductAvailability = NULL;
        $timeSlotsCategory        = json_decode($this->timeSlots);

        if (!empty($this->time_no_limit_category) && $this->time_no_limit_category != 'false') {
            $checkProductAvailability = new CheckProductAvailabilityRule($timeSlotsCategory, $this->time_no_limit);
        }

        return [
            'name'              => 'required',
            'price'             => 'required|numeric|min:0|regex:/^\d*(\.\d{2})?$/',
            'vat_id'            => 'required',
            'category_id'       => 'required',
            'uploadAvatar'      => 'mimes:jpeg,jpg,png',
            'days'              => [$checkProductAvailability],
            'days.*.start_time' => 'date_format:H:i',
            'days.*.end_time'   => 'date_format:H:i|after:days.*.start_time',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name'        => trans('product.validation.name'),
            'price'       => trans('product.validation.price'),
            'vat_id'      => trans('product.validation.btw_type'),
            'category_id' => trans('product.validation.categories'),
        ];
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
            'price.regex'           => trans('product.validation.price_regex'),
        ];
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator, true)));
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator|void
     */
    public function prepareForValidation()
    {
        $inputSwitch = array();

        if (!$this->has('use_category_option')) {
            $inputSwitch['use_category_option'] = 0;
        }

        if (!$this->has('veggie')) {
            $inputSwitch['veggie'] = 0;
        }

        if (!$this->has('vegan')) {
            $inputSwitch['vegan'] = 0;
        }

        if (!$this->has('spicy')) {
            $inputSwitch['spicy'] = 0;
        }

        if (!$this->has('new')) {
            $inputSwitch['new'] = 0;
        }

        if (!$this->has('promo')) {
            $inputSwitch['promo'] = 0;
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
            $langScan['description'] = $this->description;
            $this->merge([$langKey => $langScan]);
        }
    }
}
