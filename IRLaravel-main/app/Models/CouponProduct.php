<?php

namespace App\Models;

class CouponProduct extends AppModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'coupon_products';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id',
        'product_id',
        'coupon_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function coupon()
    {
        return $this->belongsTo(\App\Models\Coupon::class, 'coupon_id');
    }
}
