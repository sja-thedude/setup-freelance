<?php

namespace App\Models;

class CartOptionItem extends AppModel
{
    public $table = 'cart_option_items';
    
    public $timestamps = true;

    public $fillable = [
        'id',
        'cart_item_id',
        'product_id',
        'optie_id',
        'optie_item_id',
        'workspace_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'cart_item_id'  => 'integer',
        'product_id'    => 'integer',
        'optie_id'      => 'integer',
        'optie_item_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'cart_item_id' => 'required|integer',
        'product_id'    => 'required|integer',
        'optie_id'      => 'required|integer',
        'optie_item_id' => 'required|integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cartItem()
    {
        return $this->belongsTo(\App\Models\CartItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'optie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function optieItem()
    {
        return $this->belongsTo(\App\Models\OptionItem::class, 'optie_item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function optionItem()
    {
        return $this->belongsTo(\App\Models\OptionItem::class, 'optie_item_id');
    }
}
