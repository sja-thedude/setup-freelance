<?php

namespace App\Http\Requests;

use App\Traits\APIResponse;
use App\Models\Group;
use Carbon\Carbon;

class UpdateGroupRequest extends Request
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
        $discountValidations = $percentageValidations = [];
        if (in_array($this->discount_type, [Group::PERCENTAGE, Group::FIXED_AMOUNT])) {
            $discountValidations = ['required_without:percentage'];
            $percentageValidations = ['required_without:discount'];
            if (!empty($this->discount)) {
                $discountValidations = array_merge($discountValidations, ['numeric', 'min:0']);
            }

            if (!empty($this->percentage)) {
                $percentageValidations = array_merge($percentageValidations, ['integer', 'min:0']);
            }
        }

        $productValidations = 'nullable';
        if (empty($this->get('products')) && empty($this->get('category_ids')) && $this->get('is_product_limit')) {
            $productValidations = 'required';
        }

        return array_merge(Group::$rules, [
            'name'             => 'required|min:3',
            'company_street'   => 'required',
            'company_number'   => 'required',
            'company_postcode' => 'required',
            'company_city'     => 'required',
            'close_time'       => 'required|date_format:H:i',
            'receive_time'     => 'required|date_format:H:i',
            'contact_email'    => 'required|email',
            'contact_surname'  => 'required|max:255',
            'contact_name'     => 'required|max:255',
            'contact_gsm'      => array_merge(['required'], config('validation.phone')),
            'discount'   => $discountValidations,
            'percentage'       => $percentageValidations,
            'discount_type'    => 'required',
            'products'    => $productValidations,
        ]);
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

        if (!$this->has('payment_mollie')) {
            $inputSwitch['payment_mollie'] = 0;
        }

        if (!$this->has('payment_payconiq')) {
            $inputSwitch['payment_payconiq'] = 0;
        }

        if (!$this->has('payment_cash')) {
            $inputSwitch['payment_cash'] = 0;
        }

        if (!$this->has('payment_factuur')) {
            $inputSwitch['payment_factuur'] = 0;
        }

        if (!$this->has('is_product_limit')) {
            $inputSwitch['is_product_limit'] = 0;
        }

        $inputSwitch['close_time'] = Carbon::parse($this->close_time)->format('H:i');
        $inputSwitch['receive_time'] = Carbon::parse($this->receive_time)->format('H:i');

        $this->merge($inputSwitch);
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'products.required'           => trans('coupon.valid_product'),
        ];
    }
}
