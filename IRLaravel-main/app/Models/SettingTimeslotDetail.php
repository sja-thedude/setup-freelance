<?php

namespace App\Models;

use App\Facades\Helper;
use Carbon\Carbon;

class SettingTimeslotDetail extends AppModel
{
    public $table = 'setting_timeslot_details';

    public $fillable = [
        'workspace_id',
        'setting_timeslot_id',
        'type',
        'active',
        'time',
        'max',
        'date',
        'repeat',
        'day_number',
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
        'active' => 'boolean',
        'max' => 'integer',
        'date' => 'date',
        'repeat' => 'boolean',
        'day_number' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

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
     * Mutator type_display
     *
     * @return string
     */
    public function getDateStringAttribute()
    {
        return Carbon::parse($this->date)->format("Y-m-d");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingTimeslot()
    {
        return $this->belongsTo(\App\Models\SettingTimeslot::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
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
            'active' => $this->active,
            'time' => $this->time,
            'max_order' => $this->max,
            'max_price' => $this->settingTimeslot->max_price_per_slot,
            'date' => Helper::getDateFromFormat($this->date, 'Y-m-d'),
            'day_number' => $this->day_number,
        ];
    }

}
