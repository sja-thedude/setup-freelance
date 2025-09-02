<?php

namespace App\Models;

class SettingExceptHourTranslation extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'setting_except_hour_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'setting_except_hour_id',
        'locale',
        'description',
        'created_at',
        'updated_at',
    ];
}
