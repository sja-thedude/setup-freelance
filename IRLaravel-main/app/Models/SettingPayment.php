<?php

namespace App\Models;

class SettingPayment extends AppModel
{
    const TYPE_MOLLIE = 0;
    const TYPE_PAYCONIQ = 1;
    const TYPE_CASH = 2;
    const TYPE_FACTUUR = 3;

    const VALUE_FALSE = 0;
    const VALUE_TRUE = 1;

    public $table = 'setting_payments';

    public $fillable = [
        'workspace_id',
        'type',
        'api_token',
        'takeout',
        'delivery',
        'in_house',
        'self_ordering',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'integer',
        'api_token' => 'string',
        'takeout' => 'boolean',
        'delivery' => 'boolean',
        'in_house' => 'boolean',
        'self_ordering' => 'boolean',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentReferences()
    {
        return $this->hasMany(\App\Models\SettingPaymentReference::class, 'local_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class, 'setting_payment_id');
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_MOLLIE => trans('setting.payment_methods.mollie'),
            static::TYPE_PAYCONIQ => trans('setting.payment_methods.payconiq'),
            static::TYPE_CASH => trans('setting.payment_methods.cash'),
            static::TYPE_FACTUUR => trans('setting.payment_methods.op_factuur'),
        ];
    }

    /**
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        return array_get($this->getTypes(), $this->type, $this->type);
    }

    /**
     * Check if payment method is online payment
     *
     * @param int $paymentMethod
     * @return bool
     */
    public static function isOnlinePayment(int $paymentMethod)
    {
        return in_array($paymentMethod, [
            static::TYPE_MOLLIE,
            static::TYPE_PAYCONIQ,
        ]);
    }

    /**
     * @return array
     */
    public function getFullInfo()
    {
        return [
            'id' => $this->getKey(),
            'type' => $this->type,
            'type_display' => $this->type_display,
            'api_token' => $this->api_token,
            'takeout' => $this->takeout,
            'delivery' => $this->delivery,
            'in_house' => $this->in_house,
            'self_ordering' => $this->self_ordering,
        ];
    }

}
