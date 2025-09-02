<?php

namespace App\Models;

class NotificationDevice extends AppModel
{
    public $fillable = [
        'created_at',
        'updated_at',
        'active',
        'user_id',
        'template_id',
        'group_restaurant_id',
        'device_id',
        'token',
        'notified',
        'type',
        'model',
        'model_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'user_id' => 'integer',
        'template_id' => 'integer',
        'group_restaurant_id' => 'integer',
        'device_id' => 'string',
        'token' => 'string',
        'notified' => 'boolean',
        'type' => 'integer',
        'model' => 'string',
        'model_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'device_id' => 'required',
        'token' => 'required',
        'type' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function template()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    public function groupRestaurant()
    {
        return $this->belongsTo(\App\Models\GroupRestaurant::class);
    }
}
