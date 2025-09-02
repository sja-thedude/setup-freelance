<?php

namespace App\Models;

class SettingGeneral extends AppModel
{
    public $table = 'setting_generals';
    
    const PRIMARY_COLOR = '#413E38';
    const SECOND_COLOR = '#B5B268';

    public $fillable = [
        'workspace_id',
        'title',
        'subtitle',
        'primary_color',
        'second_color',
        'created_at',
        'updated_at',
        'instellingen',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'subtitle' => 'string',
        'primary_color' => 'string',
        'second_color' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

}
