<?php

namespace App\Models;

use Lang;

class ProductLabel extends AppModel
{
    const VEGGIE = 1;
    const VEGAN = 2;
    const SPICY = 3;
    const NEWW = 4;
    const PROMO = 5;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'product_labels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'product_id',
        'type',
        'active',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return [
            static::VEGGIE => trans('product.product_labels.' . static::VEGGIE),
            static::VEGAN => trans('product.product_labels.' . static::VEGAN),
            static::SPICY => trans('product.product_labels.' . static::SPICY),
            static::NEWW => trans('product.product_labels.' . static::NEWW),
            static::PROMO => trans('product.product_labels.' . static::PROMO),
        ];
    }

    /**
     * @return string
     */
    public function getTypeDisplayAttribute()
    {
        $key = 'product.product_labels.' . $this->type;
        $text = $this->type;

        if (Lang::has($key)) {
            $text = trans($key);
        }

        return $text;
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
        ];
    }

}
