<?php

namespace App\Models;

use App\Helpers\OrderHelper;

class SettingOpenHour extends AppModel
{
    const TYPE_TAKEOUT = 0;
    const TYPE_DELIVERY = 1;
    const TYPE_IN_HOUSE = 2;

    const TAKEOUT = 'takeout'; // TAKOUT
    const LEVERING = 'levering'; // DELIVERY
    const GROUP = 'group'; // GROUP

    public $table = 'setting_open_hours';

    public $fillable = [
        'created_at',
        'updated_at',
        'type',
        'active',
        'workspace_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'workspace_id' => 'integer',
        'active' => 'boolean',
        'type' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required|integer',
        'type' => 'required|integer',
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
    public function openTimeSlots()
    {
        return $this->hasMany(\App\Models\OpenTimeslot::class, 'foreign_id')
            ->where('foreign_model', \App\Models\SettingOpenHour::class);
    }

        /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openTimeSlotsOrderStartTime()
    {
        return $this->hasMany(\App\Models\OpenTimeslot::class, 'foreign_id')
            ->where('foreign_model', \App\Models\SettingOpenHour::class)
            ->orderBy('start_time', 'ASC');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openHourReferences()
    {
        return $this->hasMany(\App\Models\SettingOpenHourReference::class, 'local_id');
    }

    /**
     * @param int|null $value
     * @return array|string
     */
    public static function getTypes($value = null)
    {
        $options = array(
            static::TYPE_TAKEOUT => trans('setting_open_hour.types.takeout'),
            static::TYPE_DELIVERY => trans('setting_open_hour.types.delivery'),
            static::TYPE_IN_HOUSE => trans('setting_open_hour.types.in_house'),
            OrderHelper::TYPE_SELF_ORDERING => trans('order.types.self_ordering'),
        );
        return static::enum($value, $options);
    }
}
