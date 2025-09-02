<?php

namespace App\Http\Requests;

use Illuminate\Support\Carbon;
use InfyOm\Generator\Request\APIRequest;
use App\Traits\APIResponse;

/**
 * Class RegisterRequest
 *
 * @package App\Http\Requests
 */
class UpdateProfileRequest extends APIRequest
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
        $validationPhone = config('validation.phone');
        $validationName = config('validation.name');

        if ($this->required_only_gsm) {
            $rules = [
                'gsm'        => array_merge(['required'], $validationPhone),
                'first_name' => array_merge(['required'], $validationName),
            ];
        } else {
            $rules = [
                'first_name' => array_merge(['required'], $validationName),
                'last_name'  => array_merge(['required'], $validationName),
                'email'      => 'required|string|email|max:255' . (auth()->user()->email !== $this->email ? "|unique:users" : ""),
                'birthday'   => 'nullable|date_format:d/m/Y|before:13 years ago|after:-100 years',
//                'gender' => 'required',
                'address'    => 'nullable|string',
                'gsm'        => array_merge(['required'], $validationPhone),
            ];
        }
        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'first_name' => trans('frontend.voornaam'),
            'last_name'  => trans('frontend.naam'),
            'email'      => trans('frontend.email'),
            'birthday'   => trans('frontend.geb_datum'),
            'gender'     => trans('frontend.geslacht'),
            'address'    => trans('frontend.adres'),
            'gsm'        => trans('frontend.gsm'),
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
            'birthday.before' => trans('user.validation.birthday_13'),
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

        $formatDate = str_replace('/', '-', $this->birthday);
        $date = Carbon::parse($formatDate)->format('Y-m-d');
        $dateNow = Carbon::parse($date)->addYears(13)->format('Y-m-d');

        if ($dateNow === Carbon::now()->format('Y-m-d')) {
            $inputSwitch['birthday'] = Carbon::parse($date)->addDays(1)->format('d/m/Y');
        }

        $this->merge($inputSwitch);
    }
}
