<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\SettingOpenHour;
use App\Traits\APIResponse;

class UpdateSettingOpenHourRequest extends Request
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
        $type = $this->get('type');
        $rule = [];

        if($type == 'open-hour-time-slots') {
            $rule = [
                'data.*.start_time' => 'required|date_format:H:i',
                'data.*.end_time' => 'required|date_format:H:i|after:data.*.start_time'
            ];
        }

        return $rule;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'data.*.end_time.after' => trans('validation.after_progess_time'),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $type = $this->get('type');
        $input = $this->all();

        if($type == 'open-hour-time-slots') {
            if(!empty($input['start_end_time'])) {
                $data = [];
                $slotData = $input['slot_id'];

                foreach($input['start_end_time'] as $dayKey => $dayItem) {
                    if(!empty($dayItem)) {
                        foreach($dayItem as $key => $startEndTime) {
                            $splitTime = explode(' - ', $startEndTime);
                            $data[] = [
                                'id' => $slotData[$dayKey][$key],
                                'day_number' => $dayKey,
                                'start_time' => $splitTime[0],
                                'end_time' => $splitTime[1]
                            ];
                        }
                    }
                }

                $this->merge([
                    'data' => $data
                ]);
            }
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->sendError(trans('common.form_invalid'), 400, $this->apiFailedValidation($validator)));
    }
}
