<?php

namespace App\Models;

use App\Facades\Helper;

class SettingExceptHour extends AppModel
{
    public $table = 'setting_except_hours';

    public $fillable = [
        'workspace_id',
        'start_time',
        'end_time',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'date',
        'end_time' => 'date',
        'description' => 'string'
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
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'start_time' => Helper::getDateFromFormat($this->start_time, 'Y-m-d'),
            'end_time' => Helper::getDateFromFormat($this->end_time, 'Y-m-d'),
            'description' => $this->description,
        ];
    }

}
