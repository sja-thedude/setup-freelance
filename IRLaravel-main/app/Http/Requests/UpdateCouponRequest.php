<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Traits\APIResponse;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends Request
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
        $minOfTimeAll = $this->max_time_single;
        $tmpUser = session('auth_temp');
        $codeValidations = [
            [
                'required',
                'max:255',
            ]
        ];
        $coupon = Coupon::findOrFail($this->coupon);
        if ($coupon->code != $this->code) {
            $codeValidations[] = Rule::unique('coupons')->where(function ($query) use ($tmpUser, $coupon) {
                return $query->where('workspace_id', $tmpUser->workspace_id)->where('code', $this->code)->where('id', '!=', $coupon->id)->whereNull('deleted_at');
            });
        }

        $discountValidations = ['required_without:percentage'];
        $percentageValidations = ['required_without:discount'];
        if (!empty($this->discount)) {
            $discountValidations = array_merge($discountValidations, ['numeric', 'min:0']);
        }

        if (!empty($this->percentage)) {
            $percentageValidations = array_merge($percentageValidations, ['integer', 'min:0']);
        }

        $productValidations = 'nullable';
        if (empty($this->get('category')) && empty($this->get('category_ids'))) {
            $productValidations = 'required';
        }

        return array_merge(Coupon::$rules, [
            'code'              => $codeValidations,
            'promo_name'        => 'required|max:255',
            'max_time_single'   => 'required|integer|min:0',
            'max_time_all'      => 'required|integer|min:' . $minOfTimeAll,
            'discount'          => $discountValidations,
            'percentage'        => $percentageValidations,
            'expire_time_valid' => 'required|after:' . Carbon::now($this->timeZone)->format('Y-m-d H:i'),
            'category'          => $productValidations
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'code'              => trans('coupon.lb_code'),
            'promo_name'        => trans('coupon.lb_promo_name'),
            'max_time_single'   => trans('coupon.lb_max_time_single'),
            'max_time_all'      => trans('coupon.lb_max_time_all'),
            'discount'          => trans('coupon.lb_discount'),
            'percentage'        => trans('coupon.lb_percentage'),
            'expire_time_valid' => trans('coupon.lb_expire_time'),
        ];
    }

    public function messages()
    {
        return [
            'code.required'               => trans('common.validation.field_required'),
            'promo_name.required'         => trans('common.validation.field_required'),
            'max_time_single.required'    => trans('common.validation.field_required'),
            'max_time_all.required'       => trans('common.validation.field_required'),
            'discount.required_without'   => trans('common.validation.field_required'),
            'percentage.required_without' => trans('common.validation.field_required'),
            'expire_time_valid.required'  => trans('common.validation.field_required'),
            'expire_time_valid.after'     => trans('validation.after', ['attribute' => trans('coupon.lb_expire_time'), 'date' => Carbon::now($this->timeZone)->format('d/m/Y H:i')]),
            'category.required'           => trans('coupon.valid_product'),
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

        if ($this->has('expire_time')) {
            $inputSwitch['expire_time_valid'] = Carbon::parse(Helper::convertDateTimeToTimezone($this->expire_time, $this->timeZone))
                ->format('Y-m-d H:i');

            $inputSwitch['expire_time'] = Carbon::parse(Helper::convertDateTimeToUTC(str_replace("/", "-", $this->range_send_datetime), $this->timeZone))
                ->format('Y-m-d H:i');
        }

        if (!$this->has('active')) {
            $inputSwitch['active'] = 0;
        }

        $inputSwitch['max_time_single'] = $this->max_time_single === "0" ? 0 : ltrim($this->max_time_single, '0');
        $inputSwitch['max_time_all']    = $this->max_time_all === "0" ? 0 : ltrim($this->max_time_all, '0');

        $this->merge($inputSwitch);
    }
}
