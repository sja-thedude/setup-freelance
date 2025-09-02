<?php

namespace App\Models;

use App\Facades\Helper;
use Carbon\Carbon;
use Lang;

class OpenTimeslot extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'open_timeslots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'workspace_id',
        'foreign_id',
        'foreign_model',
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'day_number',
        'status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class, 'workspace_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class, 'open_timeslot_id');
    }

    /**
     * @return string
     */
    public function getDayNumberDisplayAttribute()
    {
        $key = 'common.days.' . $this->day_number;
        $text = $this->day_number;

        if (Lang::has($key)) {
            $text = trans($key);
        }

        return $text;
    }

    /**
     * @return string
     */
    public function getStartTimeConvertAttribute()
    {
        return $this->start_time ? Carbon::parse($this->start_time)->format("H:i") : "00:00";
    }

    /**
     * @return string
     */
    public function getEndTimeConvertAttribute()
    {
        return $this->end_time ? Carbon::parse($this->end_time)->format("H:i") : "23:59";
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        // Correct day of week
        $this->day_number = Helper::correctDayOfWeek($this->day_number);

        return [
            'id' => $this->getKey(),
            'start_time' => Helper::getTimeFromFormat($this->start_time, 'H:i:s'),
            'end_time' => Helper::getTimeFromFormat($this->end_time, 'H:i:s'),
            'day_number' => $this->day_number,
            'day_number_display' => $this->day_number_display,
            'status' => $this->status,
        ];
    }

    /**
     * Fire events when create, update roles
     * The "booting" method of the model.
     * @link https://stackoverflow.com/a/38685534
     *
     * @overwrite
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->start_time = $model->start_time ?: "00:00";
            $model->end_time   = $model->end_time ?: "00:00";

            return $model;
        });

        static::updating(function ($model) {
            $model->start_time = $model->start_time ?: "00:00";
            $model->end_time = $model->end_time ?: "00:00";

            return $model;
        });
    }
}
