<?php

namespace App\Models;

class NotificationCategory extends AppModel
{
    public $table = 'notification_category';
    
    public $timestamps = false;

    public $fillable = [
        'notification_id',
        'restaurant_category_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @param $notification
     * @param $input
     */
    public static function syncCategories($notification, $input){
        $datas = [];
        if(!empty($input)) {
            foreach ($input as $id) {
                $datas[$id] = array(
                    'notification_id' => $notification->id,
                    'restaurant_category_id' => (int) $id
                );
            }
        }
        $notification->notificationCategories()->sync($datas);
    }
}
