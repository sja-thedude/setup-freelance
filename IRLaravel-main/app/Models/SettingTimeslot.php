<?php

namespace App\Models;

class SettingTimeslot extends AppModel
{
    const TIMESLOT_BEFORE_DAY_0 = 0;
    const TIMESLOT_BEFORE_DAY_1 = 1;
    const TIMESLOT_BEFORE_DAY_2 = 2;

    public $table = 'setting_timeslots';

    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'integer',
        'order_per_slot' => 'integer',
        'max_price_per_slot' => 'decimal',
        'interval_slot' => 'integer',
        'max_mode' => 'boolean',
        'max_time' => 'time',
        'max_before' => 'integer',
        'max_days' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settingTimeslotDetails()
    {
        return $this->hasMany(\App\Models\SettingTimeslotDetail::class);
    }

    public static function getBeforeDays()
    {
        return [
            static::TIMESLOT_BEFORE_DAY_0 => trans('time_slot.before_day_' . static::TIMESLOT_BEFORE_DAY_0),
            static::TIMESLOT_BEFORE_DAY_1 => trans('time_slot.before_day_' . static::TIMESLOT_BEFORE_DAY_1),
            static::TIMESLOT_BEFORE_DAY_2 => trans('time_slot.before_day_' . static::TIMESLOT_BEFORE_DAY_2),
        ];
    }

    const TYPE_TAKEOUT = 0;
    const TYPE_DELIVERY = 1;
    const TYPE_IN_HOUSE = 2;

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_TAKEOUT => trans('delivery_types.take_out'),
            static::TYPE_DELIVERY => trans('delivery_types.delivery'),
            static::TYPE_IN_HOUSE => trans('delivery_types.in_house'),
        ];
    }

    /**
     * Mutator type_display
     *
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        $types = static::getTypes();

        return (array_key_exists($this->type, $types)) ? $types[$this->type] : $this->type;
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'type' => $this->type,
            'type_display' => $this->type_display,
            'order_per_slot' => $this->order_per_slot,
            'max_price_per_slot' => $this->max_price_per_slot,
            'interval_slot' => $this->interval_slot,
            'max_mode' => $this->max_mode,
            'max_time' => $this->max_time,
            'max_before' => $this->max_before,
            'max_days' => array_map('intval', explode(',', $this->max_days)),
        ];
    }

}
