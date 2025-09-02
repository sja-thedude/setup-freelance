<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Reward;
use App\Traits\APIResponse;
use Carbon\Carbon;

class UpdateRewardRequest extends Request
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
        $rule = [
            'title'             => 'required|max:255',
            'type'              => 'required|integer|min:0',
            'score'             => 'required|integer|min:0',
            'expire_date_valid' => 'required|after:' . Carbon::now($this->timeZone)->format('Y-m-d H:i'),
        ];

        $rewardValidations = ['required_without:percentage'];
        $percentageValidations = ['required_without:reward'];
        if (!empty($this->reward)) {
            $rewardValidations = array_merge($rewardValidations, ['numeric', 'min:0']);
        }

        if (!empty($this->percentage)) {
            $percentageValidations = array_merge($percentageValidations, ['integer', 'min:0']);
        }

        if ($this->type == Reward::KORTING) {
            $rule['reward']      = $rewardValidations;
            $rule['percentage']  = $percentageValidations;

            $productValidations = 'nullable';
            if (empty($this->get('category')) && empty($this->get('category_ids'))) {
                $productValidations = 'required';
            }

            $rule['category'] = $productValidations;
        }

        if ($this->type == Reward::FYSIEK_CADEAU) {
            $rule['description'] = 'required';
        }

        return $rule;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title'             => trans('reward.lb_title'),
            'score'             => trans('reward.table.credits_nodig'),
            'reward'            => trans('reward.lb_beloning_waarde'),
            'expire_date_valid' => trans('reward.lb_expire_time'),
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

        if ($this->has('expire_date')) {
            $inputSwitch['expire_date_valid'] = Carbon::parse(Helper::convertDateTimeToTimezone($this->expire_date, $this->timeZone))
                ->format('Y-m-d H:i');

            $inputSwitch['expire_date'] = Carbon::parse(Helper::convertDateTimeToUTC(str_replace("/", "-", $this->range_send_datetime), $this->timeZone))
                ->format('Y-m-d H:i');
        }

        if (!$this->has('repeat')) {
            $inputSwitch['repeat'] = 0;
        }

        $inputSwitch['score'] = $this->score === "0" ? 0 : ltrim($this->score, '0');

        $this->merge($inputSwitch);
    }
}
