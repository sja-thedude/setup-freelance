<?php

namespace App\Models;

class WorkspaceExtra extends AppModel
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    const PAYCONIQ = 0;
    const GROUP_ORDER = 1;
    const CUSTOMER_CARD = 2;
    const ALLERGENEN = 3;
    const SMS_WHATSAPP = 4;
    const DISPLAY_IN_APP = 5;
    const OWN_MOBILE_APP = 6;
    const STICKER = 7;
    const CONNECTORS = 8;
    const WEBSITE_V2 = 9;
    const TABLE_ORDERING = 10;
    const SELF_SERVICE = 11;
    const SELF_ORDERING = 12;
    const SERVICE_COST = 13;

    public $table = 'workspace_extras';

    public $fillable = [
        'workspace_id',
        'active',
        'type',
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
        'workspace_id' => 'integer',
        'active' => 'boolean',
        'type' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required|integer',
        'type' => 'required|integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @param int|null $value
     * @return array|string
     */
    public static function getTypes($value = null)
    {
        $options = array(
            // static::PAYCONIQ => trans('workspace.payconiq'),
            static::STICKER => trans('workspace.sticker'),
            static::GROUP_ORDER => trans('workspace.group_order'),
            static::CUSTOMER_CARD => trans('workspace.customer_card'),
            static::ALLERGENEN => trans('workspace.allergens'),
            static::SMS_WHATSAPP => trans('workspace.sms_whatsapp'),
            static::DISPLAY_IN_APP => trans('workspace.display_in_app'),
            static::OWN_MOBILE_APP => trans('workspace.own_mobile_app'),
            static::CONNECTORS => trans('workspace.connectors'),
            static::WEBSITE_V2 => trans('workspace.website_v2'),
            static::TABLE_ORDERING => trans('workspace.table_ordering'),
            static::SELF_SERVICE => trans('workspace.self_service'),
            static::SELF_ORDERING => trans('workspace.self_ordering'),
            static::SERVICE_COST => trans('workspace.service_cost'),
        );
        return static::enum($value, $options);
    }

    /**
     * Get type as string key
     *
     * @param int $type
     * @return string
     */
    public static function getTypeString(int $type)
    {
        $strType = '';

        switch ($type) {
            case static::TABLE_ORDERING:
                $strType = 'table_ordering';
                break;
            case static::SELF_SERVICE:
                $strType = 'self_service';
                break;
            default:
                break;
        }

        return $strType;
    }

    /**
     * @param $workspaceId
     * @param $type
     * @return mixed
     */
    public static function getOneExtraByType($workspaceId, $type)
    {
        $data = static::where('workspace_id', $workspaceId)
            ->where('type', $type)->first();

        return $data;
    }

    /**
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        return static::getTypes($this->type);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'workspace_id' => $this->workspace_id,
            'active' => !empty($this->active),
            'type' => $this->type,
            'type_display' => $this->type_display,
        ];
    }

}
