<?php

namespace App\Models;

class SettingPreferenceTranslation extends AppModel
{
    public $table = 'setting_preference_translations';

    protected $fillable = [
        'holiday_text',
        'table_ordering_pop_up_text',
        'self_ordering_pop_up_text',
    ];
}
