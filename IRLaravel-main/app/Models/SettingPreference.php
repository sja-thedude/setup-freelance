<?php

namespace App\Models;

use App\Facades\Helper;
use Dimsav\Translatable\Translatable;

class SettingPreference extends AppModel
{
    use Translatable;

    public $table = 'setting_preferences';

    const INACTIVE = 0;
    const ACTIVE = 1;

    const TYPE_TAKEOUT = 0;
    const TYPE_DELIVERY = 1;
    const TYPE_IN_HOUSE = 2;

    public $fillable = [
        'workspace_id',
        'takeout_min_time',
        'takeout_day_order',
        'delivery_min_time',
        'delivery_day_order',
        'mins_before_notify',
        'use_sms_whatsapp',
        'use_email',
        'receive_notify',
        'sound_notify',
        'opties_id',
        'holiday_text',
        'table_ordering_pop_up_text',
        'self_ordering_pop_up_text',
        'service_cost_set',
        'service_cost',
        'service_cost_amount',
        'service_cost_always_charge',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'takeout_min_time' => 'integer',
        'takeout_day_order' => 'integer',
        'delivery_min_time' => 'integer',
        'delivery_day_order' => 'integer',
        'mins_before_notify' => 'integer',
        'use_sms_whatsapp' => 'boolean',
        'use_email' => 'boolean',
        'receive_notify' => 'boolean',
        'sound_notify' => 'boolean',
        'holiday_text' => 'string',
        'table_ordering_pop_up_text' => 'string',
        'self_ordering_pop_up_text' => 'string',
        'service_cost_set' => 'boolean',
        'service_cost_always_charge' => 'boolean',
    ];

    public $translatedAttributes = [
        'holiday_text',
        'table_ordering_pop_up_text',
        'self_ordering_pop_up_text',
    ];

    /**
     * The relations to eager load on every query.
     * (optionally)
     *
     * @var array
     */
    protected $with = ['translations'];

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
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'opties_id');
    }

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
        $tableOrderingExtra = $this->workspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::TABLE_ORDERING)->first();
        $tableOrderingStatus = $tableOrderingExtra ? $tableOrderingExtra->active : null;
        $selfOrderingExtra = $this->workspace->workspaceExtras->where('type', \App\Models\WorkspaceExtra::SELF_ORDERING)->first();
        $selfOrderingStatus = $selfOrderingExtra ? $selfOrderingExtra->active : null;

        return [
            'id' => $this->id,
            'created_at' => Helper::getDatetimeFromFormat($this->created_at, 'Y-m-d H:i:s'),
            'updated_at' => Helper::getDatetimeFromFormat($this->updated_at, 'Y-m-d H:i:s'),
            'takeout_min_time' => $this->takeout_min_time,
            'takeout_day_order' => $this->takeout_day_order,
            'delivery_min_time' => $this->delivery_min_time,
            'delivery_day_order' => $this->delivery_day_order,
            'mins_before_notify' => $this->mins_before_notify,
            'use_sms_whatsapp' => $this->use_sms_whatsapp,
            'use_email' => $this->use_email,
            'receive_notify' => $this->receive_notify,
            'sound_notify' => $this->sound_notify,
            'option_id' => $this->opties_id,
            'holiday_text' => $this->holiday_text,
            'table_ordering_pop_up_text' => $tableOrderingStatus ? $this->table_ordering_pop_up_text : null,
            'self_ordering_pop_up_text' => $selfOrderingStatus ? $this->self_ordering_pop_up_text : null,
        ];
    }
}
