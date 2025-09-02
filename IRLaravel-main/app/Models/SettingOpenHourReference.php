<?php

namespace App\Models;

class SettingOpenHourReference extends AppModel
{
    public $table = 'setting_open_hour_references';

    public $fillable = [
        'workspace_id',
        'local_id',
        'provider',
        'remote_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'workspace_id' => 'integer',
        'local_id' => 'integer',
        'provider' => 'string',
        'remote_id' => 'string',

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required|integer',
        'local_id' => 'required|integer',
        'provider' => 'required|string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingOpenHour()
    {
        return $this->belongsTo(\App\Models\SettingOpenHour::class, 'local_id');
    }
}
