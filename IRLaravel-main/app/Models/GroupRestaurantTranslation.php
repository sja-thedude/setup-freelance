<?php

namespace App\Models;

class GroupRestaurantTranslation extends AppModel
{
    public $table = 'group_restaurant_translations';

    protected $fillable = [
        'name',
        'description',
    ];
}
