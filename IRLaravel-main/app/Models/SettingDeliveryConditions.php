<?php

namespace App\Models;

use App\Facades\Helper;

class SettingDeliveryConditions extends AppModel
{
    public $table = 'setting_delivery_conditions';

    public $fillable = [
        'area_start',
        'area_end',
        'price_min',
        'price',
        'free',
        'workspace_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'area_start' => 'integer',
        'area_end' => 'integer',
        'price_min' => 'float',
        'price' => 'float',
        'free' => 'float',
        'workspace_id' => 'integer',
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
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'area_start' => $this->area_start,
            'area_end' => $this->area_end,
            'price_min' => Helper::formatCurrencyNumber($this->price_min),
            'price' => Helper::formatCurrencyNumber($this->price),
            'free' => Helper::formatCurrencyNumber($this->free),
            'workspace_id' => $this->workspace_id,
            'workspace' => (!empty($this->workspace_id) && !empty($this->workspace)) ? $this->workspace->getSummaryInfo() : null,
        ];
    }

}
