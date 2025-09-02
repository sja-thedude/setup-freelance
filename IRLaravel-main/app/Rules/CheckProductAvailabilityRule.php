<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CheckProductAvailabilityRule implements Rule
{
    /**
     * @var
     */
    protected $timeSlotsCategory;

    /**
     * @var
     */
    protected $timeNoLimit;

    /**
     * CheckProductAvailabilityRule constructor.
     *
     * @param $timeSlotsCategory
     * @param $timeNoLimit
     */
    public function __construct($timeSlotsCategory, $timeNoLimit)
    {
        $this->timeSlotsCategory = json_decode(json_encode($timeSlotsCategory), true);
        $this->timeNoLimit       = $timeNoLimit;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $values
     * @return bool
     */
    public function passes($attribute, $values)
    {
        // Validate when choose "product always available"
        if(!$this->timeNoLimit) {
            if(!empty($this->timeSlotsCategory)) {
                foreach ($this->timeSlotsCategory as $categorySlot) {
                    if (empty($categorySlot['status'])) {
                        return false;
                    }
                }
            }

            return true;
        }

        if (!$values) {
            return true;
        }

        // Validate time slots
        foreach ($values as $dayProduct) {
            if (isset($dayProduct['status']) && $dayProduct['status']) {

                $dayNumberProduct = $dayProduct['day_number'];
                $dayCategory      = $this->timeSlotsCategory[$dayNumberProduct];

                if (!$dayCategory['status']) {
                    return false;
                }

                $startTimeProduct = Carbon::parse($dayProduct['start_time'])->format('H:i');
                $endTimeProduct   = Carbon::parse($dayProduct['end_time'])->format('H:i');
                $startTimeCate    = $dayCategory['start_time_convert'];
                $endTimeCate      = $dayCategory['end_time_convert'];

                if ($startTimeProduct < $startTimeCate || $endTimeProduct > $endTimeCate) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('product.validation.days');
    }
}
