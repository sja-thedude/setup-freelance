<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\SettingTimeslot;
use App\Models\SettingTimeslotDetail;
use Carbon\Carbon;

class SettingTimeslotRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'type',
        'order_per_slot',
        'max_price_per_slot',
        'interval_slot',
        'max_mode',
        'max_time',
        'max_before',
        'max_days',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingTimeslot::class;
    }

    // BEGIN - Main time slots
    public function getTimeSlotByWorkspace($workspaceId) {
        return $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->whereIn('type', config('common.setting_types_have_timeslots'))
            ->get();
    }

    public function initTimeSlotForWorkspace($workspaceId) {
        $checkExist = $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->whereIn('type', config('common.setting_types_have_timeslots'))
            ->get();

        if($checkExist->isEmpty()) {
            $now = now();
            $data = [];
            $types = config('common.setting_types_have_timeslots');
            $common = [
                'workspace_id' => $workspaceId,
                'order_per_slot' => 1,
                'max_price_per_slot' => 100,
                'interval_slot' => 10,
                'max_mode' => 0,
                'max_time' => '15:00:00',
                'max_before' => 0,
                'max_days' => null,
                'created_at' => $now,
                'updated_at' => $now
            ];

            foreach($types as $typeLabel => $type) {
                $common['type'] = $type;
                $data[] = $common;
            }

            if(!empty($data)) {
                SettingTimeslot::insert($data);
            }
        }

        return true;
    }

    public function updateTimeSlots($setting, $input) {
        if(!empty($input)) {
            $triggerGenerate = false;
            $deleteConds = [];
            unset($input['_token']);
            $input['max_mode'] = !empty($input['max_mode']);

            if(isset($input['max_days'])) {
                if(!empty($input['max_days'])) {
                    $input['max_days'] = implode(',', $input['max_days']);
                } else {
                    $input['max_days'] = '';
                }
            } else {
                if(!empty($input['max_mode'])) {
                    $input['max_days'] = '';
                }
            }

            if($setting->interval_slot != $input['interval_slot']) {
                $deletedTimeslotDetails = $setting->settingTimeslotDetails->pluck('id')->toArray();
                /** Update delete_timeslot flag to define the orders which are impacted by changing timeslots */
                Order::whereIn('setting_timeslot_detail_id', $deletedTimeslotDetails)->update(['deleted_timeslot' => 1]);
                /** @var SettingTimeslotDetail $deletedTimeslotDetail */

                $setting->settingTimeslotDetails()->delete();
                $triggerGenerate = true;
                $deleteConds[] = [
                    ['field' => 'setting_timeslot_id', 'cond' => 'where', 'value' => $setting->id]
                ];
            }
            if($setting->order_per_slot != $input['order_per_slot']) {
                $setting->settingTimeslotDetails()->update(['max' => $input['order_per_slot']]);
            }

            $setting->fill($input);
            $setting->save();

            if(!empty($triggerGenerate)) {
                dispatch(new \App\Jobs\TriggerGenerateSixMonthTimeSlots($setting->workspace_id, $deleteConds, [], $setting));
            }
        }

        return true;
    }
    // END - Main time slots

    // BEGIN - Detail time slots
    public function initTimeSlotDetail($workspaceId, $dateConvert, $dayOfWeek, $settingTimeSlot, $timeSlotArrs) {
        $tsDateDetails = $settingTimeSlot->settingTimeslotDetails()
            ->where('date', $dateConvert)
            ->orderBy('time', 'ASC')
            ->get();

        if($tsDateDetails->isEmpty()) {
            $data = [];
            $currentDate = Carbon::now();
            $checkDetail = $settingTimeSlot->settingTimeslotDetails()
                ->where('day_number', $dayOfWeek)
                ->where('date', '<=', $dateConvert)
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->first();

            $times = [];

            if(!empty($checkDetail)) {
                $tsDayNumberDetails = $settingTimeSlot->settingTimeslotDetails()
                    ->where('date', $checkDetail->date)
                    ->where('repeat', true)
                    ->get();

                if(!$tsDayNumberDetails->isEmpty()) {
                    foreach($tsDayNumberDetails as $tsDayNumberDetail) {
                        $times[$tsDayNumberDetail->time] = [
                            'active' => $tsDayNumberDetail->active,
                            'max' =>  $tsDayNumberDetail->max
                        ];
                    }
                }
            }

            if(!empty($timeSlotArrs)) {
                foreach($timeSlotArrs as $timeSlotArr) {
                    $timeConvert = date('H:i:s', strtotime($timeSlotArr));
                    $active = true;
                    $max = $settingTimeSlot->order_per_slot;
                    $repeat = false;

                    if(!empty($times) && !empty($times[$timeConvert])) {
                        $active = $times[$timeConvert]['active'];
                        $max = $times[$timeConvert]['max'];
                        $repeat = true;
                    }

                    $data[] = [
                        'workspace_id' => $workspaceId,
                        'setting_timeslot_id' => $settingTimeSlot->id,
                        'type' => $settingTimeSlot->type,
                        'time' => $timeSlotArr,
                        'date' => $dateConvert,
                        'day_number' => $dayOfWeek,
                        'active' => $active,
                        'max' => $max,
                        'repeat' => $repeat,
                        'created_at' => $currentDate,
                        'updated_at' => $currentDate
                    ];
                }
            }

            if(!empty($data)) {
                SettingTimeslotDetail::insert($data);
            }

            $tsDateDetails = $settingTimeSlot->settingTimeslotDetails()
                ->where('date', $dateConvert)
                ->orderBy('time', 'ASC')
                ->get();
        }

        return $tsDateDetails;
    }

    public function updateTimeSlotDetail($input) {
        if(!empty($input['data'])){
            $data = $input['data'];

            foreach($data as $item) {
                $item['repeat'] = !empty($item['repeat']);
                $item['active'] = !empty($item['active']);
                $itemId = $item['id'];

                $timeSlotDetail = SettingTimeslotDetail::find($itemId);
                $time = $item['time'].":00";
                $type = $item['type'];
                if (!empty($item['repeat'])) {
                    unset($item['id']);
                    unset($item['time']);
                    unset($item['type']);

                    SettingTimeslotDetail::where('workspace_id', $timeSlotDetail->workspace_id)
                        ->where('day_number', $timeSlotDetail->day_number)
                        ->where('time', $time)
                        ->where('type', $type)
                        ->update($item);
                } else {
                    SettingTimeslotDetail::where('id', $itemId)->update($item);

                    SettingTimeslotDetail::where('workspace_id', $timeSlotDetail->workspace_id)
                        ->where('day_number', $timeSlotDetail->day_number)
                        ->where('time', $item['time'])
                        ->where('type', $item['type'])
                        ->update(['repeat' => $item['repeat']]);
                }
            }
        }
    }
    // END - Detail time slots
}
