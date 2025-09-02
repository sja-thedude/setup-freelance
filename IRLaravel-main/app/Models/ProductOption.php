<?php

namespace App\Models;

class ProductOption extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'product_opties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'product_id',
        'opties_id',
        'is_checked',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function option()
    {
        return $this->belongsTo(\App\Models\Option::class, 'opties_id');
    }
}
