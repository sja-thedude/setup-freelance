<?php

namespace App\Models;

class NotificationPlan extends AppModel
{
    public $table = 'notification_plans';

    public $fillable = [
        'id',
        'workspace_id',
        'platform',
        'title',
        'description',
        'is_send_everyone',
        'location',
        'location_lat',
        'location_long',
        'location_radius',
        'send_now',
        'is_sent',
        'send_datetime',
        'gender_dest_male',
        'gender_dest_female',
        'start_age_dest',
        'end_age_dest',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'platform' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'is_send_everyone' => 'boolean',
        'location' => 'string',
        'location_lat' => 'string',
        'location_long' => 'string',
        'location_radius' => 'integer',
        'send_now' => 'integer',
        'is_sent' => 'boolean',
        'gender_dest_male' => 'integer',
        'gender_dest_female' => 'integer',
        'start_age_dest' => 'integer',
        'end_age_dest' => 'integer'
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
     **/
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notificationCategories()
    {
        return $this->belongsToMany(
            \App\Models\RestaurantCategory::class,
            'notification_category',
            'notification_id',
            'restaurant_category_id'
        );
    }
}
