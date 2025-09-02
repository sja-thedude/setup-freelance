<?php

namespace App\Models;

use App\Facades\Helper;

class Notification extends AppModel
{
    const ADMIN = 0;
    const MANAGER = 1;
    const CLIENT = 2;

    const UNCHECKED_GENDER = 0;
    const CHECKED_GENDER = 1;

    /**
     * Read status
     */
    const ACTIVE = 1;
    /**
     * Unread status
     */
    const INACTIVE = 0;

    public $table = 'notifications';

    public $fillable = [
        'notification_plan_id',
        'template_id',
        'group_restaurant_id',
        'platform',
        'status',
        'title',
        'description',
        'sent_time',
        'user_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'notification_plan_id' => 'integer',
        'template_id' => 'integer',
        'group_restaurant_id' => 'integer',
        'platform' => 'integer',
        'status' => 'boolean',
        'title' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|max:255',
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
    public function notificationPlan()
    {
        return $this->belongsTo(\App\Models\NotificationPlan::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function template()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        $sentTime = Helper::getDatetimeFromFormat($this->sent_time, 'Y-m-d H:i:s');

        if(empty($this->sent_time) && !empty($this->notificationPlan)) {
            $sentTime = Helper::getDatetimeFromFormat($this->notificationPlan->send_datetime, 'Y-m-d H:i:s');
        }

        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'status' => $this->status,
            'title' => $this->title,
            'description' => $this->description,
            'sent_time' => $sentTime,
        ];
    }

    /**
     * @return array
     */
    public function getPushNotificationMetaData()
    {
        $sentTime = Helper::getDatetimeFromFormat($this->sent_time, 'Y-m-d H:i:s');

        if(empty($this->sent_time) && !empty($this->notificationPlan)) {
            $sentTime = Helper::getDatetimeFromFormat($this->notificationPlan->send_datetime, 'Y-m-d H:i:s');
        }

        return [
            'id' => $this->getKey(),
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'status' => $this->status,
            'title' => $this->title,
            'description' => $this->description,
            'sent_time' => $sentTime,
        ];
    }

    /**
     * Count unread notification by user
     *
     * @param int $userId
     * @return int
     */
    public static function getCountByUser(int $userId)
    {
        return static::where('user_id', $userId)
            ->where('status', false)
            ->count();
    }
}
