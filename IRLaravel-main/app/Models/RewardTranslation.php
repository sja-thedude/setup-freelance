<?php

namespace App\Models;

class RewardTranslation extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'reward_level_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'reward_level_id',
        'locale',
        'title',
        'description',
        'created_at',
        'updated_at',
    ];
}
