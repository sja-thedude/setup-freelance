<?php

namespace App\Repositories;

use App\Models\SettingPreference;
use App\Models\SettingTimeslotDetail;

class SettingTimeslotDetailRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'setting_timeslot_id',
        'type',
        'active',
        'time',
        'max',
        'date',
        'repeat',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingTimeslotDetail::class;
    }

    /**
     * Get timeslots by date
     *
     * @param int $workspaceId
     * @param string $date
     * @param int|null $type
     * @return \Illuminate\Support\Collection
     */
    public function getTimeslotsByDate(int $workspaceId, string $date, $type = null)
    {
        $timeslots = $this->model
            ->with(['settingTimeslot'])
            ->where('workspace_id', $workspaceId)
            ->where('date', $date);

        if (!empty($type)) {
            $timeslots = $timeslots->where('type', $type);
        }

        $timeslots = $timeslots->get();
        /** @var \Illuminate\Support\Collection $timeslots */

        return $timeslots;
    }

    /**
     * @param array $timeslotIds
     * @return array
     */
    public function calculateTimeslotsByOrders(array $timeslotIds)
    {
        $orders = \App\Models\Order::whereIn('setting_timeslot_detail_id', $timeslotIds)
            ->where(function ($individualOrder) {
                /** @var \Illuminate\Database\Eloquent\Builder $individualOrder */
                $individualOrder
                    // Order type: individual
                    ->whereNull('orders.group_id')
                    // Order type: group
                    ->orWhere(function ($groupOrder) {
                        /** @var \Illuminate\Database\Eloquent\Builder $groupOrder */
                        $groupOrder->whereNotNull('orders.group_id')
                            ->whereNotNull('orders.parent_id');
                    });
            })
            ->where(function ($paymentInfo) {
                /** @var \Illuminate\Database\Eloquent\Builder $paymentInfo */
                $paymentInfo
                    // Payment method online with status is paid
                    ->where('status', \App\Models\Order::PAYMENT_STATUS_PAID)
                    // Or use payment method is cash / invoice
                    ->orWhereIn('payment_method', [\App\Models\SettingPayment::TYPE_CASH, \App\Models\SettingPayment::TYPE_FACTUUR]);
            })
            ->groupBy('setting_timeslot_detail_id')
            ->select('setting_timeslot_detail_id')
            ->addSelect(\DB::raw('(COUNT(id)) AS current_order'))
            ->addSelect(\DB::raw('(SUM(total_price)) AS current_price'))
            ->get();

        $data = [];

        foreach ($orders as $order) {
            $data[$order->setting_timeslot_detail_id] = [
                'current_order' => $order->current_order,
                'current_price' => $order->current_price,
            ];
        }

        return $data;
    }

    /**
     * Check timeslot by order days
     *
     * @param int $workspaceId
     * @param int $type
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function checkTimeslotOrderDays(int $workspaceId, int $type, $options = [])
    {
        // Get workspace detail
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = \App\Models\Workspace::where('id', $workspaceId)
            ->active()
            ->with(['settingPreference'])
            ->first();

        if (empty($workspace)) {
            throw new \Exception(trans('workspace.not_found'));
        }

        // Setting reference of workspace
        $settingPreference = $workspace->settingPreference;

        // Haven't configured
        if (empty($settingPreference)) {
            // Init default settings
            $settingPreference = new SettingPreference([
                'workspace_id' => $workspaceId,
                'takeout_min_time' => config('settings.preferences.takeout_min_time'),
                'takeout_day_order' => config('settings.preferences.takeout_day_order'),
                'delivery_min_time' => config('settings.preferences.delivery_min_time'),
                'delivery_day_order' => config('settings.preferences.delivery_day_order'),
            ]);
        }

        $maxOrderDays = 0;
        $minWaitingTime = 0;

        // Get max order days by order type
        if ($type == SettingTimeslotDetail::TYPE_TAKEOUT) {
            $maxOrderDays = $settingPreference->takeout_day_order;
            $minWaitingTime = $settingPreference->takeout_min_time;
        } else if ($type == SettingTimeslotDetail::TYPE_DELIVERY) {
            $maxOrderDays = $settingPreference->delivery_day_order;
            $minWaitingTime = $settingPreference->delivery_min_time;
        }

        $orderDays = [];
        $currentDay = \Carbon\Carbon::now();

        // Check timeslot in today
        $timezone = array_get($options, 'timezone', config('app.timezone'));
        $minTimeslot = $currentDay->copy()->tz($timezone)->addMinutes($minWaitingTime);
        $todayTimeslots = \App\Models\SettingTimeslotDetail::active()
            ->where('workspace_id', $workspaceId)
            ->where('type', $type)
            ->where('date', $minTimeslot->toDateString())
            ->where('time', '>=', $minTimeslot->toTimeString())
            ->count();

        for ($i = 0; $i < $maxOrderDays; $i++) {
            // Next day
            $currentDay->addDay();
            // Push order days from current to array
            $orderDays[] = $currentDay->toDateString();
        }

        // Query to check exist
        $fieldCounter = 'total_available_timeslot';
        $timeslots = SettingTimeslotDetail::whereIn('date', $orderDays)
            ->where('workspace_id', $workspaceId)
            ->where('type', $type)
            ->where('active', true)
            ->where('max', '>', 0)
            ->groupBy('date')
            ->select('date')
            ->addSelect(\DB::raw("COUNT(id) AS {$fieldCounter}"))
            ->get();

        $idxTimeslots = [];

        /** @var \App\Models\SettingTimeslotDetail $timeslot */
        foreach ($timeslots as $timeslot) {
            $idxTimeslots[$timeslot->date->toDateString()] = $timeslot->getAttribute($fieldCounter);
        }

        $data = [];
        // Current day
        $data[$minTimeslot->toDateString()] = ($todayTimeslots > 0);

        // Fill empty timeslot
        foreach ($orderDays as $day) {
            $data[$day] = array_get($idxTimeslots, $day, 0) > 0;
        }

        return $data;
    }

}
