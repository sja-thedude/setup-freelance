<?php

namespace App\Models;

class OrderItem extends AppModel
{
    const TYPE_TAKEOUT = 0;
    const TYPE_DELIVERY = 1;
    const TYPE_IN_HOUSE = 2;

    public $table = 'order_items';

    public $fillable = [
        'workspace_id',
        'order_id',
        'category_id',
        'product_id',
        'type',
        'price',
        'total_number',
        'subtotal',
        'total_price',
        'paid',
        'vat_percent',
        'coupon_id',
        'coupon_discount',
        'redeem_history_id',
        'ship_price',
        'metas',
        'created_at',
        'updated_at',
        'group_discount',
        'available_discount',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'workspace_id' => 'integer',
        'order_id' => 'integer',
        'category_id' => 'integer',
        'product_id' => 'integer',
        'type' => 'integer',
        'price' => 'decimal',
        'total_number' => 'integer',
        'subtotal' => 'decimal',
        'total_price' => 'decimal',
        'paid' => 'boolean',
        'vat_percent' => 'decimal',
        'coupon_id' => 'integer',
        'coupon_discount' => 'decimal',
        'redeem_history_id' => 'integer',
        'ship_price' => 'decimal',
        'metas' => 'string',
        'group_discount' => 'decimal',
        'available_discount' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'workspace_id' => 'required|integer',
        'order_id' => 'required|integer',
        'category_id' => 'required|integer',
        'product_id' => 'required|integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class)->withTrashed()
            ->with('translations');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class)->withTrashed()
            ->with('translations');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(\App\Models\Coupon::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function redeemHistory()
    {
        return $this->belongsTo(\App\Models\RedeemHistory::class);
    }

    public function optionItems()
    {
        return $this->hasMany(\App\Models\OrderOptionItem::class, 'order_item_id');
    }
}
