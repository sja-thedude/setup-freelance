<?php

namespace App\Models;

class ProductAllergenen extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'product_allergenens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'product_id',
        'allergenen_id',
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
    public function allergenen()
    {
        return $this->belongsTo(\App\Models\Allergenen::class, 'allergenen_id');
    }
}
