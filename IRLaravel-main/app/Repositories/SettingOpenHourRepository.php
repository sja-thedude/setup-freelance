<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\SettingOpenHour;
use App\Models\OpenTimeslot;
use App\Models\SettingOpenHourReference;
use App\Models\SettingTimeslot;
use App\Models\SettingTimeslotDetail;
use App\Helpers\Helper;

class SettingOpenHourRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'created_at',
        'updated_at',
        'type',
        'active',
        'workspace_id'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingOpenHour::class;
    }

    /**
     * @param int $workspaceId
     * @return \Illuminate\Support\Collection
     */
    public function getOpenHourByWorkspace(int $workspaceId)
    {
        return $this->model
            ->where('workspace_id', $workspaceId)
            ->with(['openTimeSlots' => function ($query) {
                $query->orderBy('day_number', 'ASC')
                    ->orderBy('start_time', 'ASC');
            }])
            ->get();
    }

    public function initOpenHourForWorkspace($workspaceId) {
        $checkExist = $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();

        if($checkExist->isEmpty()) {
            $now = now();
            $data = [];
            $types = SettingOpenHour::getTypes();
            $common = [
                'active' => true,
                'workspace_id' => $workspaceId,
                'created_at' => $now,
                'updated_at' => $now
            ];

            foreach($types as $type => $typeLabel) {
                $common['type'] = $type;
                $data[] = $common;
            }

            if(!empty($data)) {
                SettingOpenHour::insert($data);
            }
        }

        return true;
    }

    public function changeActiveSetting($setting) {
        $setting->active = !$setting->active;
        $setting->save();
    }

    public function updateTimeSlots($setting, $input) {
        $triggerGenerateSixMonthTimeSlot = false;
        $deleteConds = [];
        $relatedOrders = [];
        $addedOpenTimeslot = [];

        if(!empty($input['data'])) {
            $data = $input['data'];
            $ids = [];

            foreach($data as $key => $slot) {
                if(!empty($slot['id'])) {
                    $ids[] = $slot['id'];
                }

                $data[$key]['workspace_id'] = $setting->workspace_id;
                $data[$key]['foreign_id'] = $setting->id;
                $data[$key]['foreign_model'] = SettingOpenHour::class;
                $data[$key]['status'] = true;
            }

            $removeOpenTimeSlots = $setting->openTimeSlots()
                ->whereNotIn('id', $ids)
                ->get();

            if(!$removeOpenTimeSlots->isEmpty()) {
                $dayNumbers = $removeOpenTimeSlots->pluck('day_number')->toArray();
                // Clear time slot detail
                $deletedTimeslotOrderIds = SettingTimeslotDetail::where('workspace_id', $setting->workspace_id)
                    ->where('type', $setting->type)
                    ->whereIn('day_number', $dayNumbers)->get()->pluck('id')->toArray();

                if (!empty($deletedTimeslotOrderIds)) {
                    Order::whereIn('setting_timeslot_detail_id', $deletedTimeslotOrderIds)->update(['deleted_timeslot' => 1]);
                }

                unset($deletedTimeslotOrderIds);
                unset($deletedTimeslotDetails);

                SettingTimeslotDetail::where('workspace_id', $setting->workspace_id)
                    ->where('type', $setting->type)
                    ->whereIn('day_number', $dayNumbers)
                    ->delete();

                $setting->openTimeSlots()
                    ->whereNotIn('id', $ids)
                    ->delete();
                $triggerGenerateSixMonthTimeSlot = true;
                $deleteConds[] = [
                    ['field' => 'workspace_id', 'cond' => 'where', 'value' => $setting->workspace_id],
                    ['field' => 'type', 'cond' => 'where', 'value' => $setting->type],
                    ['field' => 'day_number', 'cond' => 'whereIn', 'value' => $dayNumbers]
                ];
            }

            if(!empty($data)) {
                $newDayNumber = [];

                foreach($data as $item) {
                    $id = $item['id'];
                    unset($item['id']);

                    if(!empty($id)) {
                        $openTimeSlot = OpenTimeslot::find($id);

                        if(!empty($openTimeSlot)) {
                            if(date('H:i', strtotime($openTimeSlot->start_time)) != date('H:i', strtotime($item['start_time']))
                                || date('H:i', strtotime($openTimeSlot->end_time)) != date('H:i', strtotime($item['end_time']))) {
                                $openTimeSlot->fill($item);
                                $openTimeSlot->save();

                                if(!in_array($item['day_number'], $newDayNumber)) {
                                    $newDayNumber[] = $item['day_number'];
                                }
                            }
                        } else {
                            $id = null;
                        }
                    }

                    if(empty($id)) {
                        if(!in_array($item['day_number'], $newDayNumber)) {
                            $newDayNumber[] = $item['day_number'];
                        }

                        $addedOpenTimeslot[] = OpenTimeslot::create($item);
                    }
                }

                if(!empty($newDayNumber)) {
                    if (empty($addedOpenTimeslot)) {
                        $changedOpenTimeslots = OpenTimeslot::where('workspace_id', $setting->workspace_id)
                        ->whereIn('day_number', $newDayNumber)
                        ->where('foreign_id', $setting->id)
                        ->where('foreign_model', SettingOpenHour::class)->get();

                        foreach ($changedOpenTimeslots as $changedOpenTimeslot) {
                            $addedOpenTimeslot[] = $changedOpenTimeslot;
                        }
                    }
                    // Clear time slot detail
                    $deletedTimeslotOrderIds = SettingTimeslotDetail::where('workspace_id', $setting->workspace_id)
                        ->where('type', $setting->type)
                        ->whereIn('day_number', $newDayNumber)->get()->pluck('id')->toArray();

                    if (!empty($deletedTimeslotOrderIds)) {
                        Order::whereIn('setting_timeslot_detail_id', $deletedTimeslotOrderIds)->update(['deleted_timeslot' => 1]);
                    }

                    SettingTimeslotDetail::where('workspace_id', $setting->workspace_id)
                        ->where('type', $setting->type)
                        ->whereIn('day_number', $newDayNumber)
                        ->delete();
                    $triggerGenerateSixMonthTimeSlot = true;
                    $deleteConds[] = [
                        ['field' => 'workspace_id', 'cond' => 'where', 'value' => $setting->workspace_id],
                        ['field' => 'type', 'cond' => 'where', 'value' => $setting->type],
                        ['field' => 'day_number', 'cond' => 'whereIn', 'value' => $newDayNumber]
                    ];
                }
            }
        } else {
            $setting->openTimeSlots()->delete();
            // Clear time slot detail
            SettingTimeslotDetail::where('workspace_id', $setting->workspace_id)
                ->where('type', $setting->type)
                ->delete();
            $triggerGenerateSixMonthTimeSlot = true;
            $deleteConds[] = [
                ['field' => 'workspace_id', 'cond' => 'where', 'value' => $setting->workspace_id],
                ['field' => 'type', 'cond' => 'where', 'value' => $setting->type]
            ];
        }

        if(!empty($triggerGenerateSixMonthTimeSlot) && !empty($setting)) {
            dispatch(new \App\Jobs\TriggerGenerateSixMonthTimeSlots(
                $setting->workspace_id,
                $deleteConds,
                $relatedOrders,
                $addedOpenTimeslot
            ));
        }

        return true;
    }

    /**
     * Get open hour settings by workspace
     *
     * @param int $workspaceId
     * @return array
     */
    public function getOpenHourSettings(int $workspaceId) {
        $openHours = $this->getOpenHourByWorkspace($workspaceId);
        $idxOpenHours = [];
        $idxTimeslots = [];

        // Check exist type with activate status
        /** @var \App\Models\SettingOpenHour $openHour */
        foreach ($openHours as $openHour) {
            $idxOpenHours[$openHour->type] = $openHour->active;

            // Init $idxTimeslots with key is type if not exist
            if (array_key_exists($openHour->type, $idxTimeslots)) {
                $idxTimeslots[$openHour->type] = [];
            }

            // ------------------- Order by days of the week -------------------

            $daysOfWeekOrders = array_keys(config('days_of_week'));

            // Push order by to open timeslot collection
            $openTimeSlots = $openHour->openTimeSlots;
            $order = 0;

            foreach ($daysOfWeekOrders as $day) {
                /** @var \Illuminate\Support\Collection $tmpTimeslots */
                $tmpTimeslots = $openTimeSlots->where('day_number', $day);

                if ($tmpTimeslots->count() > 0) {
                    $tmpTimeslots->transform(function ($timeslot) use (&$order) {
                        $timeslot->order = $order++;
                        return $timeslot;
                    });
                } else {
                    $timeslot = new OpenTimeslot([
                        'id' => 0,
                        'start_time' => null,
                        'end_time' => null,
                        'day_number' => $day,
                        'status' => 1,
                    ]);
                    $timeslot->order = $order++;

                    // Push new virtual item
                    $openTimeSlots->push($timeslot);
                }
            }

            // Reorder timeslot
            $openTimeSlots = $openTimeSlots->sortBy('order');
            $openHour->setRelation('openTimeSlots', $openTimeSlots);

            // ------------------- Order by days of the week -------------------

            // Timeslots
            /** @var \App\Models\OpenTimeslot $timeslot */
            foreach ($openHour->openTimeSlots as $timeslot) {
                $idxTimeslots[$openHour->type][] = $timeslot->getFullInfo();
            }

        }

        $types = SettingOpenHour::getTypes();
        $data = [];

        /**
         * @var int $type
         * @var string $text
         */
        foreach ($types as $type => $text) {
            $data[] = [
                'type' => $type,
                'type_display' => $text,
                'active' => array_get($idxOpenHours, $type, false),
                'timeslots' => array_get($idxTimeslots, $type, []),
            ];
        }

        return $data;
    }

    /**
     * Get holidays of a settings'restaurant
     *
     * @param int $workspaceId
     * @return array
     */
    public function getHolidayExceptions(int $workspaceId) {
        $settingExceptHourRepo = new SettingExceptHourRepository(app());
        /** @var \Illuminate\Support\Collection $settings */
        $settings = $settingExceptHourRepo->getSettingByWorkspace($workspaceId);

        $settings->transform(function ($item) {
            /** @var \App\Models\SettingExceptHour $item */
            return $item->getFullInfo();
        });

        $data = $settings->toArray();

        return $data;
    }

    public function getByTimeSlotType($type, $workspaceId) {
        return $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->where('type', $type)
            ->first();
    }

    public function generateTimeSlotSixMonths(
        $workspaceId,
        $addedOpenTimeslot = [],
        $changedSettingTimeslot = null
    ) {
        $relatedOrders = [];
        $now = date('Y-m-d H:i:s');
        $yesterday = date('Y-m-d', strtotime($now . ' -1 day'));
        $maxDateTime = date('Y-m-d H:i:s', strtotime($now . ' +3 week'));
        $allTypes = config('common.setting_types_have_timeslots');
        $needInsertTimeSlotDetails = [];
        $existedTimeSlotDetails = [];
        $settingTimeSlotDetails = SettingTimeslotDetail::where('workspace_id', $workspaceId)
            ->where('date', '>', $yesterday)
            ->groupBy('type', 'date')
            ->orderBy('date', 'ASC')
            ->get();

        if(!$settingTimeSlotDetails->isEmpty()) {
            foreach ($allTypes as $type) {
                $existedTimeSlotDetails['type_'.$type] = $settingTimeSlotDetails->where('type', $type)->pluck('date_string')->all();
            }
        }

        foreach ($allTypes as $type) {
            $currentDateTime = $now;
            $settingOpenHour = $this->model
                ->where('workspace_id', $workspaceId)
                ->where('type', $type)
                ->with(['openTimeSlots' => function ($query) {
                    $query->orderBy('day_number', 'ASC')
                        ->orderBy('start_time', 'ASC');
                }])
                ->first();

            if(!empty($settingOpenHour) && !$settingOpenHour->openTimeSlots->isEmpty()) {
                $settingTimeSlot = SettingTimeslot::where('workspace_id', $workspaceId)
                    ->where('type', $type)
                    ->first();

                if(!empty($settingTimeSlot)) {
                    for($i = 1; strtotime($currentDateTime) <= strtotime($maxDateTime); $i++){
                        $dateConvert = date(config('datetime.dateFormatServer'), strtotime($currentDateTime));

                        if(empty($existedTimeSlotDetails['type_'.$type]) || !in_array($dateConvert, $existedTimeSlotDetails['type_'.$type])) {
                            $dayOfWeek = \Carbon\Carbon::parse($dateConvert . ' 00:00:00')->dayOfWeek;

                            if(!empty($settingOpenHour)) {
                                $settingOpenHourTimes = $settingOpenHour->openTimeSlots()->where('day_number', $dayOfWeek)->get();
                            }

                            $timeSlotArrs = $timeSlotTmpArrs = [];

                            if(!$settingOpenHourTimes->isEmpty()) {
                                foreach($settingOpenHourTimes as $settingOpenHourTime) {
                                    $timeSlotTmpArrs[] = [
                                        'start_time' => $settingOpenHourTime->start_time,
                                        'end_time' => $settingOpenHourTime->end_time
                                    ];
                                }

                                $timeSlotArrs = Helper::splitTime($settingTimeSlot->interval_slot, $timeSlotTmpArrs);
                            }

                            if(!empty($timeSlotArrs)) {
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

                                    $needInsertTimeSlotDetails[] = [
                                        'workspace_id' => $workspaceId,
                                        'setting_timeslot_id' => $settingTimeSlot->id,
                                        'type' => $settingTimeSlot->type,
                                        'time' => $timeSlotArr,
                                        'date' => $dateConvert,
                                        'day_number' => $dayOfWeek,
                                        'active' => $active,
                                        'max' => $max,
                                        'repeat' => $repeat,
                                        'created_at' => $now,
                                        'updated_at' => $now
                                    ];
                                }
                            }
                        }

                        $currentDateTime = date('Y-m-d H:i:s', strtotime($currentDateTime . ' +1 day'));
                    }
                }
            }
        }

        if(!empty($needInsertTimeSlotDetails)) {
            foreach (array_chunk($needInsertTimeSlotDetails, 100) as $dataInsert) {
                SettingTimeslotDetail::insert($dataInsert);
            }

            if (!empty($addedOpenTimeslot)) {
                $this->getOrdersForAddedOpenTimeslot($addedOpenTimeslot, $relatedOrders);
                $this->reAssignOrderToNewTimeslots($workspaceId, $relatedOrders);
            }

            if (!empty($changedSettingTimeslot)) {
                $this->getOrdersForChangedSettingTimeslot($relatedOrders, $workspaceId);
                $this->reAssignOrderToNewTimeslots($workspaceId, $relatedOrders);
            }
        }
    }

    /**
     * When timeslots detail are inserted, reassign the orders which are assigned to delete timeslot detail
     *
     * @param $workspaceId
     * @param array $relatedOrders
     * @return bool
     */
    public function reAssignOrderToNewTimeslots($workspaceId, array $relatedOrders = [])
    {
        if (count($relatedOrders) == 0) {
            return false;
        }

        $updatedData = $cases = $ids = [];
        /** @var Order $relatedOrder */
        foreach ($relatedOrders as $relatedOrder) {
            if (isset($relatedOrder->date_time)) {
                $datetime = Helper::separateDateTime($relatedOrder->date_time, $relatedOrder->timezone);
                $settingTimeslotDetail = SettingTimeslotDetail::where([
                    'workspace_id' => $workspaceId,
                    'date' => $datetime['date'],
                    'time' => $datetime['time'],
                    'type' => $relatedOrder->type
                ])->first();

                if ($settingTimeslotDetail) {
                    $updatedData[] = $settingTimeslotDetail->id;
                    $cases[] = "WHEN {$relatedOrder->id} THEN ?";
                    $ids[] = $relatedOrder->id;
                }
            }
        }

        if (!empty($updatedData)) {
            $cases = implode(' ', $cases);
            Order::whereIn('id', $ids)->update(['deleted_timeslot' => 0]);
            $ids = implode(',', $ids);
            $query = "UPDATE `" . Order::getTableName()
                . "` SET `setting_timeslot_detail_id` = CASE `id` " . $cases
                . " END WHERE `id` IN ({$ids})";
            \DB::update($query, $updatedData);
        }

        return true;
    }

    /**
     * Get orders which are placed in the added open timeslot
     *
     * @param $addedOpenTimeslot
     * @param $relatedOrders
     */
    private function getOrdersForAddedOpenTimeslot($addedOpenTimeslot = [], &$relatedOrders = [])
    {
        $weekdays = [];
        foreach ($addedOpenTimeslot as $timeslot) {
            $weekday = Helper::convertWeekdayToDate($timeslot->day_number);
            if (!empty($weekday)) {
                $weekdays = array_merge($weekdays, $weekday);
            }
        }

        $weekdays = array_unique($weekdays);
        if (!empty($weekdays)) {
            $orders = Order::where(['deleted_timeslot' => 1])->whereIn('date', $weekdays)->get();
            foreach ($orders as $order) {
                array_push($relatedOrders, $order);
            }
        }
    }

    /**
     * Get all orders which are impacted by changing the interval timeslot
     *
     * @param $relatedOrders
     * @param $workspaceId
     */
    protected function getOrdersForChangedSettingTimeslot(&$relatedOrders, $workspaceId)
    {
        $deletedTimeslotOrders = Order::where(['deleted_timeslot' => 1, 'workspace_id' => $workspaceId])->get();
        if ($deletedTimeslotOrders) {
            foreach ($deletedTimeslotOrders as $deletedTimeslotOrder) {
                $relatedOrders[] = $deletedTimeslotOrder;
            }
        }
    }

    /**
     * @param $input
     * @param $workspaceId
     * @param $connectorsList
     * @return bool|null
     */
    public function updateOrCreateOpenHourReferences($input, $workspaceId, $connectorsList) {
        if(empty($connectorsList)) {
            return null;
        }

        $settings = $this->getOpenHourByWorkspace((int) $workspaceId);

        foreach($settings as $setting) {
            foreach($connectorsList as $connectorItem) {
                // Get order reference..
                $openHourReference = $setting->openHourReferences()
                    ->where('provider', $connectorItem->provider)
                    ->where('local_id', $setting->id)
                    ->first();

                if(empty($openHourReference)) {
                    $openHourReference = new SettingOpenHourReference();
                    $openHourReference->workspace_id = $workspaceId;
                    $openHourReference->local_id = $setting->id;
                    $openHourReference->provider = $connectorItem->provider;
                }

                if(!empty($input['openHourReferences'][$setting->type][$connectorItem->id])) {
                    $openHourReference->remote_id = !empty($input['openHourReferences'][$setting->type][$connectorItem->id]['remote_id'])
                        ? $input['openHourReferences'][$setting->type][$connectorItem->id]['remote_id']
                        : '';
                    $openHourReference->save();
                }
            }
        }

        return true;
    }

    /**
     * @param $localIds
     * @return \Illuminate\Support\Collection
     */
    public function getSettingOpenHourReferencesByWorkspace($workspaceId) {
        return SettingOpenHourReference::where('workspace_id', $workspaceId)
            ->get();
    }
}
