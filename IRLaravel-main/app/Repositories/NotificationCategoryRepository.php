<?php

namespace App\Repositories;

use App\Models\NotificationCategory;

class NotificationCategoryRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'notification_id',
        'restaurant_category_id'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return NotificationCategory::class;
    }
}
