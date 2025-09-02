<?php

namespace App\Models;

class OrderOptionItem extends AppModel
{
    public $table = 'order_option_items';

    public $fillable = [
        'order_item_id',
        'product_id',
        'optie_id',
        'optie_item_id',
        'price',
        'metas',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'order_item_id' => 'integer',
        'product_id' => 'integer',
        'optie_id' => 'integer',
        'optie_item_id' => 'integer',
        'price' => 'decimal',
        'metas' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'order_item_id' => 'required|integer',
        'product_id' => 'required|integer',
        'optie_id' => 'required|integer',
        'optie_item_id' => 'required|integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function optieItem()
    {
        return $this->belongsTo(\App\Models\OptionItem::class, 'optie_item_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function optionItem()
    {
        return $this->belongsTo(\App\Models\OptionItem::class, 'optie_item_id')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'optie_id')->withTrashed();
    }
}
