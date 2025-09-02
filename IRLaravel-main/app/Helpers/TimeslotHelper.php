<?php

namespace App\Helpers;

use App\Models\SettingTimeslotDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class TimeslotHelper
 * @package App\Helpers
 */
class TimeslotHelper
{
    /**
     * TimeslotHelper constructor.
     */
    public function __construct()
    {
        // TODO: constructor
    }

    /**
     * Group timeslots by date
     *
     * @param Collection|array $timeslots
     * @return array
     */
    public function groupTimeslotsByDate($timeslots)
    {
        $data = [];

        /** @var SettingTimeslotDetail $timeslot */
        foreach ($timeslots as $timeslot) {
            $date = ($timeslot->date instanceof Carbon) ? $timeslot->date->toDateString() : '';

            if (!array_key_exists($date, $data)) {
                $data[$date] = [];
            }

            $data[$date][$timeslot->id] = $timeslot;
        }

        return $data;
    }

    /**
     * Validate timeslot detail when order
     *
     * @param array $timeslotDetails
     * @return bool
     */
    public function validateTimeslotDetails(array $timeslotDetails)
    {
        // Count from orders table
        $orders = \App\Models\Order::whereIn('setting_timeslot_detail_id', array_keys($timeslotDetails))
            ->where(function (Builder $paymentInfo) {
                // Payment method online with status is paid
                $paymentInfo->where('status', \App\Models\Order::PAYMENT_STATUS_PAID)
                    // Or use payment method is cash / invoice
                    ->orWhereIn('payment_method', [\App\Models\SettingPayment::TYPE_CASH, \App\Models\SettingPayment::TYPE_FACTUUR]);
            })
            ->groupBy('setting_timeslot_detail_id')
            ->select('setting_timeslot_detail_id')
            ->addSelect(\DB::raw('COUNT(id) AS current_order'), \DB::raw('SUM(total_price) AS current_price'))
            ->get();

        // When don't have any order with the timeslot id
        if (count($orders) == 0) {
            return true;
        }

        // Index timeslot counter
        $timeslotCounters = [];

        foreach ($orders as $order) {
            $timeslotCounters[$order->setting_timeslot_detail_id] = $order;
        }

        // Check with total timeslot in day
        $counterFull = 0;

        /** @var \App\Models\SettingTimeslotDetail $timeslotDetail */
        foreach ($timeslotDetails as $timeslotDetail) {
            if (array_key_exists($timeslotDetail->id, $timeslotCounters)) {
                $settingTimeslot = $timeslotDetail->settingTimeslot;
                $order = $timeslotCounters[$timeslotDetail->id];

                if ((int)$order->current_order >= (int)$timeslotDetail->max
                    && (float)$order->current_price >= (float)$settingTimeslot->max_price_per_slot) {
                    $counterFull++;
                }
            }
        }

        $isFullAllTimeslots = $counterFull == count($timeslotDetails);

        return !$isFullAllTimeslots;
    }

}
